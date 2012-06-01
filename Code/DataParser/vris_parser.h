#ifndef VRIS_PARSER_H
#define VRIS_PARSER_H

#include "parser.h"
#include "QDateTime"
#include "QFile"
#include "QTextStream"
#include "QImage"

class VRIS_Parser : public Parser
{
public:
    enum ImageTypes {
        Observed = 0,   // O
        Predicted,      // P
        Preprocessed,   // C
        DTM,            // T
        AngularVolume,  // V
        Other           // X
    };

    VRIS_Parser();
    int isValidFileVersion();
    int isValidFile();
    int parseCsv();

    QString getTotalBytes();
    QString getTrailerOffset();
    QString getTrailerSize();
    ImageTypes getImageType();
    QString getPredictMinutes();
    QString getPixelSize();
    QString getDateAndTime();
    QDateTime getDateTime(); // Alias
    QString getRadarName();
    QString getEastUppb();
    QString getNorthUppb();
    QString getHeightUppb();
    QString getStoreSlope();
    QString getStoreIcept();
    QString getStoreOffset();
    QString getStoreQuantity();
    QString getGeoLong();
    QString getGeoLat();
    QString getSignal();
    void saveImage();
    void printHeader();
};

#endif // VRIS_PARSER_H
