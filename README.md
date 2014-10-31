# Network Music Player

## What is it?

Network Music Player (or NMP for short) is a network based cross platform music manager and player.

The difference between Network Music Player and other music players: The media library and all the music files are stored on a central server with a SQL database. Your whole music library can be searched using SQL!

## Why network based?

Every user of a home network containing media center, NAS and some other computers knows the problem: You want to play your music stored on your NAS you normally play on your Desktop PC on your media center PC. The problem: Other software means different playlists, configuration, etc.

That is the part where Network Music Player comes in: It allows you to store your music on your NAS, manage it on your Desktop PC and play it on your media center in your living room.

## Components

Network Music Player is split into a few components which are connected via your network.

### Server

The server provides a central database for all players. Every player connects to the server and requests the library/playlists and audio stream from it using the REST API.

In this way, it's easy to access your whole music collection from any (internet connected) place on the world. You just have to forward the HTTP port.

### Player

The player connects to the server and plays the media in the library. A player could be your media center (e.g. an addon for XBMC/Kodi). Even DLNA support is planed!

### Web interface

The web interface is used to manage your whole media library. This includes creating (smart) playlists, edit meta data or manage the files (e.g. delete or reorganize).
