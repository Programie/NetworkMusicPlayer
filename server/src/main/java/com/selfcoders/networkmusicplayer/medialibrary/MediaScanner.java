package com.selfcoders.networkmusicplayer.medialibrary;

import com.selfcoders.networkmusicplayer.medialibrary.database.Track;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;
import org.springframework.jdbc.core.namedparam.NamedParameterJdbcTemplate;

import java.io.IOException;
import java.nio.file.FileSystems;
import java.nio.file.FileVisitResult;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.SimpleFileVisitor;
import java.nio.file.attribute.BasicFileAttributes;

public class MediaScanner {
    public static final Logger LOGGER = LogManager.getLogger(MediaScanner.class);

    private NamedParameterJdbcTemplate jdbcTemplate;

    public MediaScanner(NamedParameterJdbcTemplate jdbcTemplate) {
        this.jdbcTemplate = jdbcTemplate;
    }

    /**
     * Class used to walk the file tree while scanning for files
     */
    private class FileWalker extends SimpleFileVisitor<Path> {
        @Override
        public FileVisitResult visitFile(Path file, BasicFileAttributes attributes) {
            addFile(file);

            return FileVisitResult.CONTINUE;
        }
    }

    /**
     * Scan the specified folder recursively for new media
     * @param path The path were to start scanning
     */
    public void scan(String path) {
        try {
            Files.walkFileTree(FileSystems.getDefault().getPath(path), new FileWalker());
        } catch (IOException e) {
            LOGGER.error("Error while scanning media dir", e);
        }
    }

    /**
     * Add the specified file to the library (if not already existing)
     * @param file The file to add
     * @return true if the file has been added successfully (or already exists), false on error
     */
    private boolean addFile(Path file) {
        MediaMetadata metadata;

        String filePath = file.toAbsolutePath().toString();

        LOGGER.debug("Adding file '" + filePath + "'");

        Track track = new Track(jdbcTemplate);
        Integer trackId = track.getTrackIdByFilePath(filePath);

        if (trackId != null) {
            LOGGER.debug("File already in database: " + filePath + " -> " + trackId);
            return true;
        }

        try {
            metadata = new MediaMetadata();
            if (!metadata.readMetadata(filePath)) {
                return false;
            }
        } catch (IOException e) {
            LOGGER.error("Unable to read file", e);
            return false;
        }

        track.addTrack(filePath);

        trackId = track.getTrackIdByFilePath(filePath);

        if (trackId == null) {
            LOGGER.error("Unable to add file to tracks!");
            return false;
        }

        track.updateTrackData(trackId, metadata);

        LOGGER.debug("File successfully added");

        return true;
    }
}
