#include "parser.h"

Parser::Parser()
{
}

int Parser::setFilename(QString file, int id)
{
    m_filename = file;
    m_fileId = id;
    m_file.setFileName(m_filename);
    if (m_file.exists()) {
        return SUCCESS;
    }
    return FILE_NOT_FOUND;
}

int Parser::open()
{
    if (m_file.isOpen()) {
        return SUCCESS;
    }
    qDebug("opening file");

    if (m_filename.isEmpty())
        return MISS_FILENAME;

    if (!m_file.exists()) {
        return FILE_NOT_FOUND;
    }

    if (!m_file.open(QIODevice::ReadOnly | QIODevice::Text))
        return COULD_NOT_READ_IN_FILE;


    qDebug("file opened");

    return SUCCESS;
}

int Parser::openWrite()
{
    qDebug("opening file for write");

    if (m_filename.isEmpty())
        return MISS_FILENAME;

    if (!m_file.exists()) {
        return FILE_NOT_FOUND;
    }

    if (!m_file.open(QIODevice::WriteOnly | QIODevice::Text))
        return COULD_NOT_WRITE_IN_FILE;

    qDebug("file opened");

    return SUCCESS;
}

void Parser::close()
{
    m_file.close();
}

QString Parser::readFile(int max)
{
    if (!m_file.isOpen()) {
        if (this->open() != SUCCESS) {
            return "";
        }
    }

    return m_file.read(max).trimmed();
}

int Parser::writeOutputFile()
{
    return writeOutputFile(m_filename, "temp.csv");
}

int Parser::writeOutputFile(QString output, QString input)
{
    QFile ifile(input);
    QFile ofile(output);
    if (!ifile.open(QIODevice::ReadOnly | QIODevice::Text)) {
        qDebug("Could not read input file");
        return COULD_NOT_READ_OUT_FILE;
    }

    if (!ofile.open(QIODevice::WriteOnly | QIODevice::Text)) {
        qDebug("Could not write to output file");
        return COULD_NOT_WRITE_IN_FILE;
    }

    QTextStream out(&ofile);
    while (!ifile.atEnd())
    {
        out << ifile.readLine();
    }

    ifile.close();
    ofile.close();

    qDebug("Data written to output file");

    if (QFile::remove(input)) {
        qDebug("Input file removed");
        return SUCCESS;
    }

    return COULD_NOT_DELETE_OUT_FILE;
}
