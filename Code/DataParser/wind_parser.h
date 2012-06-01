#ifndef WIND_PARSER_H
#define WIND_PARSER_H

#include "parser.h"
#include "QtGlobal"
#include "QDateTime"
#include "QMap"
#include "QDateTime"
#include "QList"
#include "QStringList"
#include <iostream>

class Wind_Parser : public Parser
{
private:
    QList<QStringList> csv;
    QString compileSet(int key, int value);
    QString compileSet(int key, int value, QString start, QString end);

public:
    Wind_Parser();

    int isValidFile();
    int parseCsv();

};

#endif // WIND_PARSER_H
