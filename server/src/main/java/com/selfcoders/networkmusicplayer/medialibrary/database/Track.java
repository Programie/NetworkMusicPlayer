package com.selfcoders.networkmusicplayer.medialibrary.database;

import com.selfcoders.networkmusicplayer.medialibrary.MediaMetadata;
import org.springframework.dao.EmptyResultDataAccessException;
import org.springframework.jdbc.core.namedparam.MapSqlParameterSource;
import org.springframework.jdbc.core.namedparam.NamedParameterJdbcTemplate;

import java.io.IOException;

public class Track {
    private final NamedParameterJdbcTemplate jdbcTemplate;

    public Track (NamedParameterJdbcTemplate jdbcTemplate) {
        this.jdbcTemplate = jdbcTemplate;
    }

    /**
     * Get the track ID by the specified file path
     * @param filePath The absolute file path
     * @return The ID of the track or null if not found
     */
    public Integer getTrackIdByFilePath(String filePath) {
        MapSqlParameterSource parameters = new MapSqlParameterSource();
        parameters.addValue("filePath", filePath);

        try {
            return jdbcTemplate.queryForObject("SELECT `id` FROM `tracks` WHERE `filePathHash` = UNHEX(MD5(:filePath))", parameters, Integer.class);
        } catch (EmptyResultDataAccessException e) {
            return null;
        }
    }

    /**
     * Get the file path of the track specified by the given ID
     * @param trackId The ID of the track
     * @return The file path of the track or null if not found
     */
    public String getFilePathByTrackId(int trackId) {
        MapSqlParameterSource parameters = new MapSqlParameterSource();
        parameters.addValue("id", trackId);

        try {
            return jdbcTemplate.queryForObject("SELECT `filePath` FROM `tracks` WHERE `id` = :id", parameters, String.class);
        } catch (EmptyResultDataAccessException e) {
            return null;
        }
    }

    /**
     * Update the metadata from file of the track specified by the given ID
     * @param trackId The ID of the track
     * @return true on successfully update, false on error
     * @throws IOException
     */
    public boolean updateTrackData(int trackId) throws IOException {
        String filePath = this.getFilePathByTrackId(trackId);

        MediaMetadata metadata = new MediaMetadata();

        if (!metadata.readMetadata(filePath)) {
            return false;
        }

        return updateTrackData(trackId, metadata);
    }

    /**
     * Update the metadata of the track specified by the given ID
     * @param trackId The ID of the track
     * @param metadata The metadata which should be used for the given track
     * @return true on successfully update, false on error
     */
    public boolean updateTrackData(int trackId, MediaMetadata metadata) {
        MapSqlParameterSource parameters = new MapSqlParameterSource();
        parameters.addValue("title", metadata.title);
        parameters.addValue("artist", metadata.artist);
        parameters.addValue("album", metadata.album);
        parameters.addValue("genre", metadata.genre);
        parameters.addValue("year", metadata.year);
        parameters.addValue("trackNumber", metadata.trackNumber);
        parameters.addValue("trackCount", metadata.trackCount);
        parameters.addValue("diskNumber", metadata.diskNumber);
        parameters.addValue("diskCount", metadata.diskCount);
        parameters.addValue("length", metadata.length);
        parameters.addValue("id", trackId);

        jdbcTemplate.update(
                "UPDATE `tracks` " +
                "SET " +
                "`title` = :title, " +
                "`artist` = :artist, " +
                "`album` = :album, " +
                "`genre` = :genre, " +
                "`year` = :year, " +
                "`trackNumber` = :trackNumber, " +
                "`trackCount` = :trackCount, " +
                "`diskNumber` = :diskNumber, " +
                "`diskCount` = :diskCount, " +
                "`length` = :length " +
                "WHERE `id` = :id", parameters);

        return true;
    }

    /**
     * Add a new track with the specified file path
     * The metadata won't be added!
     * @param filePath The path to the file of the track
     */
    public void addTrack(String filePath) {
        MapSqlParameterSource parameters = new MapSqlParameterSource();
        parameters.addValue("filePath", filePath);

        jdbcTemplate.update("INSERT INTO `tracks` SET `filePath` = :filePath, `filePathHash` = UNHEX(MD5(:filePath)), `dateAdded` = NOW()", parameters);
    }
}
