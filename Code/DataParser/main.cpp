#include <QtCore/QCoreApplication>
#include "vris_parser.h"
#include "wind_parser.h"
#include "QTextStream"
#include "defines.h"
#include <iostream>

int main(int argc, char *argv[])
{
    QCoreApplication a(argc, argv);
    
    QStringList args = a.arguments();

    int retval = 0;
    int fileid = 0;
    QString filename = "";
    QString type = "";

    QRegExp rxArgFileId("--fileid=([0-9]{1,})");
    QRegExp rxArgFile("--file=([a-z0-9A-Z._\\\\:- ]{1,})");
    QRegExp rxArgType("--type=([a-z]{1,})");

    for (int i = 1; i < args.size(); ++i) {
        if (rxArgFileId.indexIn(args.at(i)) != -1 ) {
            fileid =  rxArgFileId.cap(1).toInt();
            qDebug() << i << ":" << args.at(i) << rxArgFileId.cap(1) << fileid;
        }
        else if (rxArgFile.indexIn(args.at(i)) != -1 ) {
            filename = rxArgFile.cap(1);
            qDebug() << i << ":" << args.at(i) << "Filename: " << filename;
        }
        else if (rxArgType.indexIn(args.at(i)) != -1 ) {
            type = rxArgType.cap(1);
            qDebug() << i << ":" << args.at(i) << "Type: " << type;
        }
        else {
            qDebug() << "Uknown arg:" << args.at(i);
            retval = 1;
        }
    }

    /*
      Stop further processing if something is missing.
      2: Failed, missing fileid
      3: Failed, missing filename
      4: Failed, missing type
    */
    if (fileid == 0) {
        qDebug("Missing file id");
        return MISS_FILEID;
    } else if (filename == "") {
        qDebug("Missing file name");
        return MISS_FILENAME;
    } else if (type == "") {
        qDebug("Missing type");
        return MISS_TYPE;
    }

    Parser *parser;
    if (type == "csv") {
        parser = new Wind_Parser();
    } else if (type == "wrk") {
        parser = new VRIS_Parser();
    } else {
        qDebug("Unknown file type");
        retval = UNK_FILE_TYPE;
    }

    retval = parser->setFilename(filename, fileid);
    if (retval != SUCCESS) {
        return retval;
    }
    if (parser->isValidFile()) {
        retval = parser->parseCsv();
    } else {
        qDebug("Invalid file");
        retval = INVALID_FILE;
    }

    return retval;
}
