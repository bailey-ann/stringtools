using NapaUtilsNet;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace ServerTermostato
{
    public class ServerTermostato
    {
        NapaMySql mysqlConnection;

        public ServerTermostato()
        {
            //istanzio l'oggetto mysql per la connessione al database
            mysqlConnection = new NapaMySql("localhost", "3306", "termostato", "root", "gabber");
            //float temperatura = mysqlConnection.QueryForFloat("select minima_automatica from temperature");
            //Console.WriteLine(temperatura);








            System.Threading.Thread.Sleep(100000);
        }
    }

    public class StatoCaldaia
    {
        public static int OFF = 0;
        public static int ON = 1;
    }
}
