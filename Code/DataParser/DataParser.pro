#-------------------------------------------------
#
# Project created by QtCreator 2012-03-02T13:11:21
#
#-------------------------------------------------

QT       += core gui sql

TARGET = DataParser
CONFIG   += console db
CONFIG   -= app_bundle

TEMPLATE = app


SOURCES += main.cpp \
    parser.cpp \
    vris_parser.cpp \
    wind_parser.cpp# \
    #Image/bmp.c

HEADERS += \
    parser.h \
    vris_parser.h \
    wind_parser.h \
    defines.h #\
    #Image/bmp.h \
    #Image/definitions.h \
    #Image/types.h


