package com.selfcoders.networkmusicplayer.server;

import com.mysql.jdbc.jdbc2.optional.MysqlDataSource;
import com.selfcoders.networkmusicplayer.medialibrary.MediaScanner;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;
import org.springframework.jdbc.core.namedparam.NamedParameterJdbcTemplate;

import java.util.Properties;

public class Server {
    public static final Logger LOGGER = LogManager.getLogger(Server.class);

    public Server(Properties configuration) {
        MysqlDataSource dataSource = new MysqlDataSource();
        dataSource.setUrl(configuration.getProperty("database.url"));
        dataSource.setUser(configuration.getProperty("database.user"));
        dataSource.setPassword(configuration.getProperty("database.password"));

        LOGGER.debug("Using database at: " + dataSource.getUrl());

        NamedParameterJdbcTemplate jdbcTemplate = new NamedParameterJdbcTemplate(dataSource);

        HttpServer httpServer = new HttpServer(Integer.valueOf(configuration.getProperty("httpport", "8090")));
        httpServer.setupService();

        if (Boolean.valueOf(configuration.getProperty("webinterface", "1"))) {
            httpServer.setupWebinterface();
        }

        MediaScanner mediaScanner = new MediaScanner(jdbcTemplate);

        String mediaDir = configuration.getProperty("mediadir");
        if (mediaDir != null) {
            mediaScanner.scan(mediaDir);// TODO: Should run automatically in a configured interval or if specified by a command line argument
        }
    }
}
