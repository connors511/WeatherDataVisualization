#ifndef PARSER_H
#define PARSER_H

#include <QtGlobal>
#include <QFile>
#include "Lib/db.h"
#include "defines.h"
#include <QTextStream>

class Parser
{
protected:
    QString m_filename;
    QFile m_file;
    int m_fileId;

public:
    Parser();
    int setFilename(QString file, int id);
    int open();
    int openWrite();
    virtual int isValidFile() = 0;
    virtual int parseCsv() = 0;
    void close();
    QString readFile(int max);
    int writeOutputFile(QString output, QString input);
    int writeOutputFile();


};

#endif // PARSER_H
