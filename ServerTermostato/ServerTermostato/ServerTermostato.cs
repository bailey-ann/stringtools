using NapaUtilsNet;
using ServerTermostato.Properties;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Timers;

namespace ServerTermostato
{
    public class ServerTermostato
    {
        private NapaMySql mysqlConnection;
        private Timer timerValori;
        private Timer timer;

        private float t_min_global = 0;
        private float t_min_automatic = 0;
        private float t_min_manual = 0;

        private int gestione_manuale = 0;

        private int stato_caldaia = 0;

        public ServerTermostato()
        {
            
            //istanzio l'oggetto mysql per la connessione al database
            mysqlConnection = new NapaMySql(Settings.Default.db_ip, Settings.Default.db_port,
                Settings.Default.db_name, Settings.Default.db_ui, Settings.Default.db_password);

            GetDatiGlobali();

            //istanzio la tipologia del gestiore rele/temperatura ( seriale / telnet )

            //istanzio il timerValori
            timerValori = new Timer(5000);
            timerValori.Elapsed += new ElapsedEventHandler(OnTimerValoriElapsed);
            timerValori.Enabled = true;

            //istanzio il timer
            timer = new Timer(5000);
            timer.Elapsed += new ElapsedEventHandler(OnTimerElapsed);
            timer.Enabled = true;






            System.Threading.Thread.Sleep(100000);
        }

        private void GetDatiGlobali()
        {
            //recupero dal db le temperature
            List<object> temperatura = mysqlConnection.QueryForSingleObjectList("SELECT `minima_gloable`, `minima_automatica`, `minima_manuale` FROM `temperature`", 3);
            t_min_global = (float)temperatura.ElementAt(0);
            t_min_automatic = (float)temperatura.ElementAt(1);
            t_min_manual = (float)temperatura.ElementAt(2);

            //recupero dal db lo stato del sistema ( manuale 1 / automatico 0 )
            gestione_manuale = mysqlConnection.QueryForInt("SELECT `manuale` FROM `gestione`");
        }

        private void OnTimerValoriElapsed(object source, ElapsedEventArgs e)
        {
            try
            {
                GetDatiGlobali();
            }
            catch (Exception ex)
            {
                Console.WriteLine(ex.Message);
            }
        }

        private void OnTimerElapsed(object source, ElapsedEventArgs e)
        {
            try
            {
                //recupero le temperatura
                float temperaturaSensore = 0;

                if (gestione_manuale == FunzioneTermostato.AUTOMATICO)
                {

                }
                else if (gestione_manuale == FunzioneTermostato.MANUALE)
                {
                    if (t_min_manual > temperaturaSensore)
                    {
                        if (stato_caldaia == StatoCaldaia.OFF)
                            Console.WriteLine("accendo");
                    }
                    else
                    {
                        if (stato_caldaia == StatoCaldaia.ON)
                            Console.WriteLine("spengo");
                    }
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine(ex.Message);
            }
        }
    }

    public class StatoCaldaia
    {
        public static int OFF = 0;
        public static int ON = 1;
    }

    public class FunzioneTermostato
    {
        public static int AUTOMATICO = 0;
        public static int MANUALE = 1;
    }
}
