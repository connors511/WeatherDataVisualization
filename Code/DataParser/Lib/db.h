#ifndef DB_H
#define DB_H

#include "QtSql/QSqlDatabase"
#include "QtSql/QSqlQuery"
#include <QDebug>
#include <QVariant>
#include <QStringList>
#include <QSqlError>


// This should be a config file
#define DB_USER "root"
#define DB_PASS ""
#define DB_HOST "localhost"
#define DB_NAME "C:/db.db"

class db
{
private:
    QSqlQuery m_query;
    QString m_preparedType;

    QString m_username;
    QString m_password;
    QString m_host;
    QString m_database;

    QSqlDatabase m_db;

public:
    db(QString username, QString password, QString host, QString database);
    ~db();

    void insertWind(QVariantList id, QVariantList ts, QVariantList pp, QVariantList ws, QVariantList rp, QVariantList op, QVariantList ro, QVariantList tsr);

};

#endif // DB_H
