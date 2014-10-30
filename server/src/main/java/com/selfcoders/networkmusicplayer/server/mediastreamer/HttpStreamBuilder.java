package com.selfcoders.networkmusicplayer.server.mediastreamer;

import javax.ws.rs.WebApplicationException;
import javax.ws.rs.core.HttpHeaders;
import javax.ws.rs.core.Response;
import javax.ws.rs.core.StreamingOutput;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.OutputStream;
import java.io.RandomAccessFile;
import java.nio.channels.Channels;
import java.nio.channels.FileChannel;
import java.nio.channels.WritableByteChannel;
import java.util.Date;

public class HttpStreamBuilder {
    private final File file;
    private final int chunkSize;

    public HttpStreamBuilder(File file, int chunkSize) {
        this.file = file;
        this.chunkSize = chunkSize;
    }

    public Response build() {
        StreamingOutput streamer = new StreamingOutput() {
            @Override
            public void write(final OutputStream output) throws IOException, WebApplicationException {

                final FileChannel inputChannel = new FileInputStream(file).getChannel();
                final WritableByteChannel outputChannel = Channels.newChannel(output);
                try {
                    inputChannel.transferTo(0, inputChannel.size(), outputChannel);
                } finally {
                    inputChannel.close();
                    outputChannel.close();
                }
            }
        };

        return Response.ok(streamer).status(200).header(HttpHeaders.CONTENT_LENGTH, file.length()).build();
    }

    public Response build(final String range) throws IOException {
        if (range == null) {
            return this.build();
        }

        String[] ranges = range.split("=")[1].split("-");
        final int from = Integer.parseInt(ranges[0]);

        int to = chunkSize + from;

        if (to >= file.length()) {
            to = (int) (file.length() - 1);
        }

        if (ranges.length == 2) {
            to = Integer.parseInt(ranges[1]);
        }

        final String responseRange = String.format("bytes %d-%d/%d", from, to, file.length());
        final RandomAccessFile randomAccessFile = new RandomAccessFile(file, "r");
        randomAccessFile.seek(from);

        final int length = to - from + 1;
        final byte[] buffer = new byte[4096];
        final StreamingOutput streamer = new StreamingOutput() {
            @Override
            public void write(OutputStream outputStream) throws IOException, WebApplicationException {
                int remaining = length;
                while (remaining != 0) {
                    int readLength = buffer.length;

                    // Read everything if the buffer larger than the data to read
                    if (buffer.length > remaining) {
                        readLength = remaining;
                    }

                    int read = randomAccessFile.read(buffer, 0, readLength);
                    outputStream.write(buffer, 0, read);

                    remaining -= read;
                }
            }
        };

        randomAccessFile.close();

        Response.ResponseBuilder response = Response.ok(streamer)
                .status(206)
                .header("Accept-Ranges", "bytes")
                .header("Content-Range", responseRange)
                .header(HttpHeaders.CONTENT_LENGTH, length)
                .header(HttpHeaders.LAST_MODIFIED, new Date(file.lastModified()));

        return response.build();
    }
}
