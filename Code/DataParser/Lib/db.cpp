#include "db.h"

db::db(QString username, QString password, QString host, QString database)
{
    m_username = username;
    m_password = password;
    m_host = host;
    m_database = database;
    m_preparedType = "NONE";

    m_db = QSqlDatabase::addDatabase("QSQLITE");
    m_db.setHostName(m_host);
    m_db.setDatabaseName(m_database);
    //m_db.setUserName(m_username);
    //m_db.setPassword(m_password);
    m_query = QSqlQuery(m_db);
    if (!m_db.open())
        qDebug() << "Failed to connect to database " << m_database.constData() << " on host " << m_host.constData();
    else
        qDebug() << "DB open";
}

db::~db()
{
    // clean up
    m_db.close();
}

void db::insertWind(QVariantList file, QVariantList ts, QVariantList pp, QVariantList ws, QVariantList rp, QVariantList op, QVariantList ro, QVariantList tsr)
{
    if (!m_db.isOpen()) {
        if (m_db.open()) {
            qDebug() << "Failed to connect to database " << m_database << " on host " << m_host << "\n";
            return;
        }
    } else {
        qDebug() << "db is open\n";
        qDebug() << m_db << m_db.tables();
    }


    if (m_preparedType != "WIND") {
        qDebug() << "Preparing statement for type WIND\n";
        if (!m_query.prepare("INSERT INTO file_csv (TimeStamps, PossiblePower, WindSpeed, RegimePossible, OutputPower, RegimeOutput, TimestampsR, files_id) VALUES(?, ?, ?, ?, ?, ?, ?, ?);")) {
            qDebug() << m_query.lastError();
        }
        m_preparedType = "WIND";
    }
    qDebug() << "prepared\n";

    m_query.bindValue(0, ts);
    m_query.bindValue(1, pp);
    m_query.bindValue(2, ws);
    m_query.bindValue(3, rp);
    m_query.bindValue(4, op);
    m_query.bindValue(5, ro);
    m_query.bindValue(6, tsr);
    m_query.bindValue(7, file);
   // qDebug() << "param count: " << ts.count() << ", " << pp.count() << ", " << ws.count() << ", " << rp.count() << ", " << op.count() << ", " << ro.count() << ", " << tsr.count() << ", " << file.count() << "\n";
    if (!m_query.execBatch()) {
        qDebug() << m_query.lastError();
    } else
        qDebug() << "executed\n";
    //m_query.execBatch();
}
