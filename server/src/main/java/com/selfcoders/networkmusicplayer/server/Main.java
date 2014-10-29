package com.selfcoders.networkmusicplayer.server;

import com.beust.jcommander.JCommander;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.Properties;

public class Main {
    public static final Logger LOGGER = LogManager.getLogger(Main.class);

    private Properties configProperties;
    
    public Main(String configurationFilePath) {
        if (!loadConfig(configurationFilePath)) {
            return;
        }

        new Server(configProperties);
    }

    /**
     * Load the configuration from file in standard properties format
     * @param configurationFilePath The path of the configuration file
     * @return true on success, false on error
     */
    public boolean loadConfig(String configurationFilePath) {
        configProperties = new Properties();

        try {
            InputStream inputStream = new FileInputStream(configurationFilePath);
            configProperties.load(inputStream);
            inputStream.close();
        } catch (IOException e) {
            LOGGER.error("Unable to read configuration file!", e);
            return false;
        }

        return true;
    }

    public static void main(String[] args) {
        CommandLineArguments commandLineArguments = new CommandLineArguments();

        JCommander jCommander = new JCommander(commandLineArguments, args);

        if (commandLineArguments.showHelp) {
            jCommander.usage();
            return;
        }

        new Main(commandLineArguments.configurationFilePath);
    }
}
