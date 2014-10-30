package com.selfcoders.networkmusicplayer.server;

import org.eclipse.jetty.server.Handler;
import org.eclipse.jetty.server.handler.ContextHandlerCollection;
import org.eclipse.jetty.servlet.ServletContextHandler;
import org.eclipse.jetty.servlet.ServletHolder;
import org.glassfish.jersey.server.ResourceConfig;
import org.glassfish.jersey.servlet.ServletContainer;

import java.util.ArrayList;
import java.util.List;

public class HttpServer {
    public final static String SERVICE_PATH = "/service";
    public final static String WEBINTERFACE_PATH = "/";

    private List<Handler> handlers = new ArrayList<>();

    public HttpServer(int port) {
        org.eclipse.jetty.server.Server server = new org.eclipse.jetty.server.Server(port);

        ContextHandlerCollection contexts = new ContextHandlerCollection();
        contexts.setHandlers(handlers.toArray(new Handler[handlers.size()]));
        server.setHandler(contexts);
    }

    /**
     * Setup the REST service and add the servlet context to the server
     */
    public void setupService() {
        ResourceConfig resourceConfig = new ResourceConfig();
        resourceConfig.packages("com.selfcoders.networkmusicplayer.server.rest");
        ServletContainer servletContainer = new ServletContainer(resourceConfig);
        ServletHolder servletHolder = new ServletHolder(servletContainer);

        ServletContextHandler context = new ServletContextHandler(ServletContextHandler.SESSIONS);
        context.setContextPath(SERVICE_PATH);
        context.addServlet(servletHolder, "/*");

        handlers.add(context);
    }

    /**
     * Setup the web interface and add the servlet context to the server
     */
    public void setupWebinterface() {
        ServletContextHandler context = new ServletContextHandler(ServletContextHandler.SESSIONS);
        context.setContextPath(WEBINTERFACE_PATH);

        // TODO: Build webinterface

        handlers.add(context);
    }
}
