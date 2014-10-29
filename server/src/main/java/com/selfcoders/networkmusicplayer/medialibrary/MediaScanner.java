package com.selfcoders.networkmusicplayer.medialibrary;

import com.selfcoders.networkmusicplayer.medialibrary.database.Track;
import java.io.File;
import java.io.IOException;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;
import org.springframework.jdbc.core.namedparam.NamedParameterJdbcTemplate;

public class MediaScanner {
    public static final Logger LOGGER = LogManager.getLogger(MediaScanner.class);

    private NamedParameterJdbcTemplate jdbcTemplate;

    public MediaScanner(NamedParameterJdbcTemplate jdbcTemplate) {
        this.jdbcTemplate = jdbcTemplate;
    }

    public void scan(String path) {
        File directory = new File(path);

        File[] fileList = directory.listFiles();

        if (fileList == null) {
            return;
        }

        for (File file : fileList) {
            if (file.isDirectory()) {
                scan(file.getAbsolutePath());
            } else {
                addFile(file);
            }
        }
    }

    private boolean addFile(File file) {
        MediaMetadata metadata;

        String filePath = file.getAbsolutePath();

        Track track = new Track(jdbcTemplate);
        Integer trackId = track.getTrackIdByFilePath(filePath);

        if (trackId != null) {
            LOGGER.debug("File already in database: " + filePath + " -> " + trackId);
            return true;
        }

        try {
            metadata = new MediaMetadata();
            if (!metadata.readMetadata(file)) {
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

        return true;
    }
}
