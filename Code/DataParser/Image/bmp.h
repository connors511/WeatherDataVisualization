/****************************************
 * BMP Header
 ****************************************/

#ifndef __BMP_H
#define __BMP_H

#include "types.h"
#include "definitions.h"

class Image
{
protected:
    QString m_filename;
    QFile m_file;
    int m_fileId;

public:
    Parser();
    int setFilename(QString file, int id);
    bool open();
    bool openWrite();
    virtual int isValidFile() = 0;
    virtual int parseCsv() = 0;
    void close();

};

/* open and read a BMP file into image */
int bmp_open(char* file, IMAGE* image);

/* store image to BMP file */
int bmp_save(char* file, IMAGE* image);

#endif /* __BMP_H */
