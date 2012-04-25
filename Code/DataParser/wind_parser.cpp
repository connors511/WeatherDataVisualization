#include "wind_parser.h"


Wind_Parser::Wind_Parser()
{
}

int Wind_Parser::isValidFile()
{
    if (!this->open()) {
        return COULD_NOT_READ_IN_FILE;
    }

    bool first = true;
    QStringList ts, pp, ws, rp, op, ro, tsr, files;

    qDebug("Reading CSV file..");

    int count = 0;
    while (!m_file.atEnd())
    {
        QString line = m_file.readLine();
        QStringList strings = line.split(",");

        if (!first) {
            ts << strings.value(0).trimmed();
            pp << strings.value(1).trimmed();
            ws << strings.value(2).trimmed();
            rp << strings.value(3).trimmed();
            op << strings.value(4).trimmed();
            ro << strings.value(5).trimmed();
            tsr << strings.value(6).trimmed();
            files << QString::number(m_fileId);
        } else {
            // Header, probably should be validated
            first = false;
            ts << strings.value(0).trimmed();
            pp << strings.value(1).trimmed();
            ws << strings.value(2).trimmed();
            rp << strings.value(3).trimmed();
            op << strings.value(4).trimmed();
            ro << strings.value(5).trimmed();
            tsr << strings.value(6).trimmed();
            files << "\"fileId\"";
        }
        if (ts.at(count).isEmpty() ||
                pp.at(count).isEmpty() ||
                ws.at(count).isEmpty() ||
                rp.at(count).isEmpty() ||
                op.at(count).isEmpty() ||
                ro.at(count).isEmpty() ||
                tsr.at(count).isEmpty()) {
            return INVALID_FILE;
        }
        count++;
    }

    this->close();

    return SUCCESS;
}

int Wind_Parser::parseCsv()
{
    if (!this->open()) {
        return COULD_NOT_READ_IN_FILE;
    }

    bool first = true;
    QStringList ts, pp, ws, rp, op, ro, tsr, files;

    qDebug("Reading CSV file..");

    int count = 0;
    while (!m_file.atEnd())
    {
        QString line = m_file.readLine();
        QStringList strings = line.split(",");

        if (!first) {
            ts << strings.value(0).trimmed();
            pp << strings.value(1).trimmed();
            ws << strings.value(2).trimmed();
            rp << strings.value(3).trimmed();
            op << strings.value(4).trimmed();
            ro << strings.value(5).trimmed();
            tsr << strings.value(6).trimmed();
            files << QString::number(m_fileId);
        } else {
            // Header, probably should be validated
            first = false;
            ts << strings.value(0).trimmed();
            pp << strings.value(1).trimmed();
            ws << strings.value(2).trimmed();
            rp << strings.value(3).trimmed();
            op << strings.value(4).trimmed();
            ro << strings.value(5).trimmed();
            tsr << strings.value(6).trimmed();
            files << "\"file_id\"";
        }
        count++;
    }

    this->close();

    qDebug() << "Read " << count << " rows";

    qDebug("Creating temp file");

    count = 0;
    QFile file("temp.csv");

    if (!file.open(QIODevice::WriteOnly | QIODevice::Text)) {
        qDebug("Could not write out file");
        return COULD_NOT_WRITE_OUT_FILE;
    }

    QTextStream out(&file);
    while(!ts.isEmpty()) {
        out << ts.takeFirst() << ",";
        out << pp.takeFirst() << ",";
        out << ws.takeFirst() << ",";
        out << rp.takeFirst() << ",";
        out << op.takeFirst() << ",";
        out << ro.takeFirst() << ",";
        out << tsr.takeFirst() << ",";
        out << files.takeFirst() << "\n";
        count++;
    }
    file.close();

    qDebug("File written");
    qDebug() << "Wrote " << count << " rows";

    return this->writeOutputFile();
}
