#include "vris_parser.h"

#include <iostream>
#include <fstream>

VRIS_Parser::VRIS_Parser() : Parser()
{
}

int VRIS_Parser::isValidFileVersion()
{
    if (!this->open()) {
        return COULD_NOT_READ_IN_FILE;
    }

    QTextStream in(&m_file);
    QString line = in.read(1);

    if (line == QString("X")) {
        return SUCCESS;
    } else {
        return FAILURE;
    }
}

int VRIS_Parser::isValidFile()
{
    // TODO: Need to check for missing / corrupt data too.
    if (this->getEastUppb().isEmpty() || this->getNorthUppb().isEmpty() ||
            this->getGeoLat().isEmpty() || this->getGeoLong().isEmpty() ||
            this->getSignal().isEmpty() || this->getTotalBytes().isEmpty() ||
            this->getPixelSize().isEmpty() || !this->getDateTime().isValid()) {
        return INVALID_FILE;
    }
    return isValidFileVersion();
}

int VRIS_Parser::parseCsv()
{
    if (!this->open()) {
        return COULD_NOT_READ_IN_FILE;
    }
    QList<QString> headers;
    headers << "signal" << "tot_bytes" << "trailer_offset" << "trailer_size";
    headers << "img_type" << "mm_predict" << "pixel_size" << "date_time";
    headers << /*"rad_name" <<*/ "east_uppb" << "north_uppb" << "hei_uppb";
    headers << "store_slope" << "store_icept" << "store_offset" << "store_quant";
    headers << /*"geo_lon" << "geo_lat" <<*/ "signal" << "pixel_values" << "file_id";

    QList<QString> elements;
    QString rad_name, geo_lon, geo_lat;
    elements << this->readFile(1); // signal
    elements << this->readFile(3); // tot_bytes
    elements << this->readFile(3); // trailer_offset
    elements << this->readFile(2); // trailer_size
    elements << this->readFile(1); // img_type
    elements << this->readFile(3); // mm_predict
    elements << this->readFile(4).replace(' ', '0'); // pixel_size
    elements << this->readFile(14); // date_time (YYYYMMDDHHMMSS)
    rad_name = this->readFile(20); // rad_name

    QString east_uppb, north_uppb;
    east_uppb = this->readFile(3);
    north_uppb = this->readFile(3);

    elements << east_uppb; // east_uppb
    elements << north_uppb; // north_uppb
    elements << this->readFile(3); // hei_uppb
    elements << this->readFile(4); // store_slope
    elements << this->readFile(6); // store_icept
    elements << this->readFile(6); // store_offset
    elements << this->readFile(8); // Store_quant
    geo_lon = this->readFile(7); // geo_lon
    geo_lat = this->readFile(7); // geo_lat
    bool ok;
    int number = this->readFile(1).toAscii().toHex().toInt(&ok, 16);
    QString tmp;
    int i = 8;
    while(i > 0)
    {
        if(number & 0x01)
        {
            tmp.prepend('1');
        }
        else
        {
            tmp.prepend('0');
        }
        i--;
        number >>= 1;
    }
    elements << tmp; // signal (signal word xxxxxxxx, 8 bits are set on/off)

    unsigned int east, north;
    east = atoi(east_uppb.toAscii());
    north = atoi(north_uppb.toAscii());
    tmp = "";
    unsigned int j;
    int val;
    for (i = 0; i < north; i++) {
        for (j = 0; j < east; j++) {
            val = m_file.read(1).toHex().toInt(&ok, 16);
            tmp.append(QString::number(val) + " ");
        }
    }
    tmp = tmp.trimmed();
    elements << tmp;
    elements << QString::number(m_fileId);

    this->printHeader();

    qDebug("Creating CSV file..");

    QFile file("temp.csv");

    if (!file.open(QIODevice::WriteOnly | QIODevice::Text)) {
        qDebug("Could not write temp file");
        return COULD_NOT_WRITE_OUT_FILE;
    }

    qDebug() << "Element count: " << elements.count();

    int count = 0;
    QTextStream out(&file);
    out << geo_lat << "," << geo_lon << "," << rad_name << "\n";
    while(!headers.isEmpty()) {
        out << headers.takeFirst();
        if (headers.count() > 0)
        {
            out << ",";
        } else {
            out << "\n";
        }
    }
    while(!elements.isEmpty()) {
        out << elements.takeFirst();
        if (elements.count() > 0)
        {
            out << ",";
        } else {
            out << "\n";
        }
        count++;
    }
    file.close();

    qDebug("File written");
    qDebug() << "Wrote " << count << " columns";

    return this->writeOutputFile(m_filename.replace(".wrk", ".csv"), "temp.csv");
}

QString VRIS_Parser::getTotalBytes()
{
    QFile m_file(m_filename);
    if (!m_file.open(QIODevice::ReadOnly | QIODevice::Text)) {
        qDebug("-- Could not open file");
    }
    m_file.seek(1);
    return m_file.peek(3);

}

QString VRIS_Parser::getTrailerOffset()
{
    QFile m_file(m_filename);
    if (!m_file.open(QIODevice::ReadOnly | QIODevice::Text)) {
        qDebug("-- Could not open file");
    }
    m_file.seek(4);
    return m_file.peek(3);
}

QString VRIS_Parser::getTrailerSize()
{
    QFile m_file(m_filename);
    if (!m_file.open(QIODevice::ReadOnly | QIODevice::Text)) {
        qDebug("-- Could not open file");
    }
    m_file.seek(7);
    return m_file.peek(2);
}

VRIS_Parser::ImageTypes VRIS_Parser::getImageType()
{
    QFile m_file(m_filename);
    if (!m_file.open(QIODevice::ReadOnly | QIODevice::Text)) {
        qDebug("-- Could not open file");
    }
    m_file.seek(9);
    char buf[1];
    m_file.peek(buf, 1);
    //std::cout << "IMG TYPE: " << buf[0] << "\n";
    if (buf[0] == 'O') {
        return Observed;
    } else if (buf[0] == 'P') {
        return Predicted;
    } else if (buf[0] == 'C') {
        return Preprocessed;
    } else if (buf[0] == 'T') {
        return DTM;
    } else if (buf[0] == 'V') {
        return AngularVolume;
    } else if (buf[0] == 'X') {
        return Other;
    }

    return Other; // Should be handled better
}

QString VRIS_Parser::getPredictMinutes()
{
    QFile m_file(m_filename);
    if (!m_file.open(QIODevice::ReadOnly | QIODevice::Text)) {
        qDebug("-- Could not open file");
    }
    m_file.seek(10);
    return m_file.peek(3);
}

QString VRIS_Parser::getPixelSize()
{
    QFile m_file(m_filename);
    if (!m_file.open(QIODevice::ReadOnly | QIODevice::Text)) {
        qDebug("-- Could not open file");
    }
    m_file.seek(13);
    return m_file.peek(4);
}

QString VRIS_Parser::getDateAndTime()
{
    QFile m_file(m_filename);
    if (!m_file.open(QIODevice::ReadOnly | QIODevice::Text)) {
        qDebug("-- Could not open file");
    }
    m_file.seek(17);
    return m_file.peek(14);
}

QDateTime VRIS_Parser::getDateTime()
{
    QString datetime = getDateAndTime();
    QDateTime dt = QDateTime::fromString(datetime, "yyyyMMddhhmmss");
    return dt;
}

QString VRIS_Parser::getRadarName()
{
    /*QFile m_file(m_filename);
    if (!m_file.open(QIODevice::ReadOnly | QIODevice::Text)) {
        qDebug("-- Could not open file");
    }*/
    this->open();
    m_file.seek(31);
    return m_file.peek(20);
}

QString VRIS_Parser::getEastUppb()
{
    QFile m_file(m_filename);
    if (!m_file.open(QIODevice::ReadOnly | QIODevice::Text)) {
        qDebug("-- Could not open file");
    }
    m_file.seek(51);
    return m_file.peek(3);
}

QString VRIS_Parser::getNorthUppb()
{
    QFile m_file(m_filename);
    if (!m_file.open(QIODevice::ReadOnly | QIODevice::Text)) {
        qDebug("-- Could not open file");
    }
    m_file.seek(54);
    return m_file.peek(3);
}

QString VRIS_Parser::getHeightUppb()
{
    QFile m_file(m_filename);
    if (!m_file.open(QIODevice::ReadOnly | QIODevice::Text)) {
        qDebug("-- Could not open file");
    }
    m_file.seek(57);
    return m_file.peek(3);
}

QString VRIS_Parser::getStoreSlope()
{
    QFile m_file(m_filename);
    if (!m_file.open(QIODevice::ReadOnly | QIODevice::Text)) {
        qDebug("-- Could not open file");
    }
    m_file.seek(60);
    return m_file.peek(4);
}

QString VRIS_Parser::getStoreIcept()
{
    QFile m_file(m_filename);
    if (!m_file.open(QIODevice::ReadOnly | QIODevice::Text)) {
        qDebug("-- Could not open file");
    }
    m_file.seek(64);
    return m_file.peek(6);
}

QString VRIS_Parser::getStoreOffset()
{
    QFile m_file(m_filename);
    if (!m_file.open(QIODevice::ReadOnly | QIODevice::Text)) {
        qDebug("-- Could not open file");
    }
    m_file.seek(70);
    return m_file.peek(6);
}

QString VRIS_Parser::getStoreQuantity()
{
    QFile m_file(m_filename);
    if (!m_file.open(QIODevice::ReadOnly | QIODevice::Text)) {
        qDebug("-- Could not open file");
    }
    m_file.seek(76);
    return m_file.peek(8);
}

QString VRIS_Parser::getGeoLong()
{
    QFile m_file(m_filename);
    if (!m_file.open(QIODevice::ReadOnly | QIODevice::Text)) {
        qDebug("-- Could not open file");
    }
    m_file.seek(84);
    return m_file.peek(7);
}

QString VRIS_Parser::getGeoLat()
{
    QFile m_file(m_filename);
    if (!m_file.open(QIODevice::ReadOnly | QIODevice::Text)) {
        qDebug("-- Could not open file");
    }
    m_file.seek(91);
    return m_file.peek(7);
}

QString VRIS_Parser::getSignal()
{
    QFile m_file(m_filename);
    if (!m_file.open(QIODevice::ReadOnly | QIODevice::Text)) {
        qDebug("-- Could not open file");
    }
    m_file.seek(98);
    bool ok;
    int number = m_file.peek(1).toHex().toInt(&ok, 16);
    QString out;
    int i = 8;
    while(i > 0)
    {
        if(number & 0x01)
        {
            out.prepend('1');
        }
        else
        {
            out.prepend('0');
        }
        i--;
        number >>= 1;
    }
    return out;
}

void VRIS_Parser::saveImage()
{
    this->open();

    unsigned int east, north;
    east = atoi(getEastUppb().toAscii());
    north = atoi(getNorthUppb().toAscii());


    QImage img(east, north, QImage::Format_Indexed8);
    img.fill(Qt::transparent);
    img.setColorCount(256);
    int i, j;
    m_file.seek(99);
    bool *ok;
    for (i = 0; i < north; i++) {
        for (j = 0; j < east; j++) {
            if (i == 0 && j == 0) qDebug() << "TEST: " << m_file.peek(1).toHex().toInt(ok, 16);
            img.setPixel(i, j, 50);//m_file.read(1).toHex().toInt(ok, 16));
        }
    }
    img.save("test.png");

    /*IMAGE img;
    img.Height = north;
    img.Width = east;
    for (qint32 i = 0; i < north * east; i++ ) {
            img.Pixels[i] = atoi(m_file.read(1).constData());
    }

    bmp_save("test.bmp", &img);*/

    //
    //image_basic *img = new image_basic(north, east);
    //img.height = north;
    //img.width = east;

    /*unsigned int i, j;
    bool *ok;
    std::cout << "Entering loop..\n";
    for (i = 0; i < north; i++) {
        for (j = 0; j < east; j++) {
            //std::cout << m_file.peek(1).toHex().toInt(ok, 16) << ", ";
            img->set(i, j, m_file.read(1).toHex().toInt(ok, 16));
        }
    }
    std::cout << "Finished loop..\n";

    std::string filename;
    filename = "test.bmp";
    img->file_write_bitmap(filename);
    free(img);*/
}

void VRIS_Parser::printHeader()
{
    std::cout << "FILE HEADER\n";
    std::cout << "Valid file version: " << (isValidFileVersion() ? "True" : "False") << "\n";
    std::cout << "Valid file: " << (isValidFile() ? "True" : "False") << "\n";
    std::cout << "Total bytes: " << getTotalBytes().toStdString() << "\n";
    std::cout << "Trailer offset: " << getTrailerOffset().toStdString() << "\n";
    std::cout << "Trailer size: " << getTrailerSize().toStdString() << "\n";
    std::cout << "Image type: " << (getImageType() == Observed ? "Observed" :
                                    getImageType() == Predicted ? "Predicted" :
                                    getImageType() == Preprocessed ? "Preprocessed" :
                                    getImageType() == DTM ? "DTM" :
                                    getImageType() == AngularVolume ? "Angular Volume" : "Other") << "\n";
    std::cout << "Predict minutes: " << getPredictMinutes().toStdString() << "\n";
    std::cout << "Pixel size: " << getPixelSize().toStdString() << "\n";
    std::cout << "Date time (string): " << getDateAndTime().toStdString() << "\n";
    std::cout << "Date time: " << getDateTime().toString().toStdString() << "\n";
    std::cout << "Radar name: " << getRadarName().toStdString() << "\n";
    std::cout << "East Uppb: " << getEastUppb().toStdString() << "\n";
    std::cout << "North Uppb: " << getNorthUppb().toStdString() << "\n";
    std::cout << "Height Uppb: " << getHeightUppb().toStdString() << "\n";
    std::cout << "Stores Slope: " << getStoreSlope().toStdString() << "\n";
    std::cout << "Store Icept: " << getStoreIcept().toStdString() << "\n";
    std::cout << "Store offset: " << getStoreOffset().toStdString() << "\n";
    std::cout << "Store Quantity: " << getStoreQuantity().toStdString() << "\n";
    std::cout << "Geo long: " << getGeoLong().toStdString() << "\n";
    std::cout << "Geo lat: " << getGeoLat().toStdString() << "\n";
    std::cout << "Signal: " << getSignal().toStdString() << "\n";
    //saveImage();
    std::cout << "FILE HEADER END\n";
}
