package com.selfcoders.networkmusicplayer.server;

import com.beust.jcommander.Parameter;

public class CommandLineArguments {
    @Parameter(names = {"-c", "--config"}, description = "Set the path to the configuration file to use", required = true)
    public String configurationFilePath = null;

    @Parameter(names = {"-h", "--help"}, description = "Show usage message", help = true)
    public boolean showHelp;
}
