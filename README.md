VelibStationsLocator
====================

A quick API to locate the nearest Velib stations in Paris. I'm going to use it for a Pebble smartwatch app but you can do whatever you want with it :)

## What is a "Velib"

Velibs are the free bike city in Paris. There's a ridiculously simplistic official API for accessing the data so I decided to build this also quite simplistic API but it returns not accessible otherwise: the closest stations to a point. I needed that to make a Pebble app that would give me the address of the closest station near me.

## Description

This "API" is basically just a wrapper for a MySQL query that is going to use the Haversine formula (http://en.wikipedia.org/wiki/Haversine_formula) to get the closest Velib stations to the poitn specified in the call.

## Installation

You need to inject the sql dump from the sql/ directory in a database and replace the XXX in the php script to your installation parameters.

## Usage

Ex: http://api.kovaxlabs.com/velib/api.php?position=48.9,2.37&limit=5&distance=1

Position is a lat,lng format point
limit is the number of results you want to retrieve
distance is the radius around the point to locate the stations (in km)

## Data

The data come from JCDecaux official website (https://developer.jcdecaux.com/#/opendata/vls?page=static), I did correct the errors contained in the CSV file and will most likely clean the capitalization because it annoys me.
