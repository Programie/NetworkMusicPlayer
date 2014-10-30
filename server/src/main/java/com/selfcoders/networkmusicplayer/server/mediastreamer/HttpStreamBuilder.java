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

    /**
     * Build a response which returns the complete data of the file
     * @return The built response object
     */
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

        // Build the response
        return Response.ok(streamer)
                .status(200)
                .header(HttpHeaders.CONTENT_LENGTH, file.length())
                .build();
    }

    /**
     * Build a response which returns the specified data range of the file
     * @param range The data range (e.g. specified by "Range" HTTP header), use null to return the complete data of the file
     * @return The built response object
     * @throws IOException
     */
    public Response build(final String range) throws IOException {
        if (range == null) {
            return this.build();
        }

        String[] ranges = range.split("=")[1].split("-");

        int rangeStart = Integer.parseInt(ranges[0]);
        int rangeEnd = chunkSize + rangeStart;

        // Set the range end if specified
        if (ranges.length == 2) {
            rangeEnd = Integer.parseInt(ranges[1]);
        }

        // Specified range end is greater or equal the length of the file
        if (rangeEnd >= file.length()) {
            rangeEnd = (int) (file.length() - 1);
        }

        final RandomAccessFile randomAccessFile = new RandomAccessFile(file, "r");
        randomAccessFile.seek(rangeStart);

        // Create an output streamer which returns the specified data range to the client
        final int length = rangeEnd - rangeStart + 1;
        final byte[] buffer = new byte[4096];
        StreamingOutput streamer = new StreamingOutput() {
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

        // Build the response
        return Response.ok(streamer)
                .status(206)
                .header("Accept-Ranges", "bytes")
                .header("Content-Range", String.format("bytes %d-%d/%d", rangeStart, rangeEnd, file.length()))
                .header(HttpHeaders.CONTENT_LENGTH, length)
                .header(HttpHeaders.LAST_MODIFIED, new Date(file.lastModified()))
                .build();
    }
}
