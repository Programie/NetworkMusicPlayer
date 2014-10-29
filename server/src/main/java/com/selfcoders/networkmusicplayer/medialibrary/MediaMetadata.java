package com.selfcoders.networkmusicplayer.medialibrary;


import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;
import org.apache.tika.Tika;
import org.apache.tika.exception.TikaException;
import org.apache.tika.metadata.Metadata;
import org.apache.tika.metadata.TikaCoreProperties;
import org.apache.tika.metadata.XMPDM;
import org.apache.tika.parser.ParseContext;
import org.apache.tika.parser.Parser;
import org.apache.tika.parser.audio.AudioParser;
import org.apache.tika.parser.mp3.Mp3Parser;
import org.xml.sax.ContentHandler;
import org.xml.sax.SAXException;
import org.xml.sax.helpers.DefaultHandler;

import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.Calendar;
import java.util.Date;
import java.util.GregorianCalendar;

public class MediaMetadata {
    public static final Logger LOGGER = LogManager.getLogger(MediaMetadata.class);

    public String title;
    public String artist;
    public String album;
    public String genre;
    public Integer year;
    public Integer trackNumber;
    public Integer trackCount;
    public Integer diskNumber;
    public Integer diskCount;
    public Integer length;

    public boolean readMetadata(InputStream inputStream) throws IOException {
        Tika tika = new Tika();
        String mediaType = tika.detect(inputStream);

        ContentHandler handler = new DefaultHandler();
        Metadata metadata = new Metadata();
        ParseContext parseContext = new ParseContext();
        Parser parser = null;

        switch (mediaType) {
            case "audio/basic":
                parser = new AudioParser();
                break;
            case "audio/x-wav":
                parser = new AudioParser();
                break;
            case "audio/x-aiff":
                parser = new AudioParser();
                break;
            case "audio/mpeg":
                parser = new Mp3Parser();
                break;
        }

        if (parser == null) {
            LOGGER.debug("No parser found for '" + mediaType + "'");
            return false;
        }

        try {
            parser.parse(inputStream, handler, metadata, parseContext);
        } catch (SAXException e) {
            LOGGER.error("Unable to process SAX events!", e);
            return false;
        } catch (TikaException e) {
            LOGGER.error("Unable to parse meta data!", e);
            return false;
        }

        Integer releaseYear = null;
        String releaseDateString = metadata.get(XMPDM.RELEASE_DATE);

        // Fix for StringIndexOutOfBoundsException in org.apache.tika.metadata.Metadata.parseDate(Metadata.java:134)
        if (releaseDateString != null && !releaseDateString.isEmpty() && releaseDateString.length() >= 3) {
            Date releaseDate = metadata.getDate(XMPDM.RELEASE_DATE);
            if (releaseDate != null) {
                Calendar releaseCalendarDate = new GregorianCalendar();
                releaseCalendarDate.setTime(releaseDate);
                releaseYear = releaseCalendarDate.get(Calendar.YEAR) - 1900;
            }
        }

        title = metadata.get(TikaCoreProperties.TITLE);
        artist = metadata.get(XMPDM.ARTIST);
        album = metadata.get(XMPDM.ALBUM);
        genre = metadata.get(XMPDM.GENRE);
        year = releaseYear;

        String trackNumberString = metadata.get(XMPDM.TRACK_NUMBER);
        if (trackNumberString != null) {
            trackNumber = Integer.valueOf(trackNumberString);
        }

        return true;
    }

    public boolean readMetadata(String filePath) throws IOException {
        LOGGER.debug("Reading meta data of '" + filePath + "'");

        InputStream inputStream = new FileInputStream(filePath);

        boolean success = readMetadata(inputStream);

        inputStream.close();

        return success;
    }
}
