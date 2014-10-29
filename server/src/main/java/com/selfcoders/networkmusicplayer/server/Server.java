package com.selfcoders.networkmusicplayer.server;

import com.selfcoders.networkmusicplayer.medialibrary.MediaScanner;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.Properties;
import javax.sql.DataSource;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;
import org.springframework.jdbc.core.namedparam.NamedParameterJdbcTemplate;

public class Server {
    public static final Logger LOGGER = LogManager.getLogger(Server.class);

    public Server(String configurationFilePath) {
        Properties properties = new Properties();

        try {
            InputStream inputStream = new FileInputStream(configurationFilePath);
            properties.load(inputStream);
            inputStream.close();
        } catch (IOException e) {
            LOGGER.error("Unable to read configuration file!", e);
            return;
        }

	// TODO
        //MySqlDataSource dataSource = new MySqlDataSource();
        //dataSource.setServerName(properties.getProperty("database.host"));
        //dataSource.setPort(properties.getProperty("database.port"));
        //dataSource.setUser(properties.getProperty("database.user"));
        //dataSource.setPassword(properties.getProperty("database.password"));
        //dataSource.setDatabaseName(properties.getProperty("database.database"));

        //NamedParameterJdbcTemplate jdbcTemplate = new NamedParameterJdbcTemplate(dataSource);

        //MediaScanner mediaScanner = new MediaScanner(jdbcTemplate);

	//String mediaDir = properties.getProperty("mediadir");
	//if (mediaDir != null) {
        //	mediaScanner.scan(mediaDir);
	//}
    }

    public static void main(String[] args) {
        if (args.length < 1) {
            System.out.println("No configuration file given!");
            return;
        }

        new Server(args[0]);
    }
}
