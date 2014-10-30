package com.selfcoders.networkmusicplayer.server.rest;

import com.selfcoders.networkmusicplayer.server.mediastreamer.HttpStreamBuilder;

import javax.ws.rs.GET;
import javax.ws.rs.HeaderParam;
import javax.ws.rs.Path;
import javax.ws.rs.PathParam;
import javax.ws.rs.Produces;
import javax.ws.rs.core.Response;
import java.io.File;
import java.io.IOException;

@Path("/stream/{id:\\d+}")
public class Stream {
    @GET
    @Produces("audio/mp3")
    public Response get(@HeaderParam("Range") String range, @PathParam("id") int id) throws IOException {
        File file = null;// TODO: Get file by ID (Requires database connection)
        HttpStreamBuilder streamBuilder = new HttpStreamBuilder(file, 1024 * 1024);

        return streamBuilder.build(range);
    }
}
