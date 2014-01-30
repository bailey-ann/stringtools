using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Diagnostics;
using System.Drawing;
using System.Linq;
using System.Reflection;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace STTrainingLight
{
    public partial class MainForm : Form
    {
        int COUNTER_COUNT_DOWN = 5;
        int STATE_TIMER;
        int DURATA_ALLENAMENTO;
        int DURATA_PAUSA;
        bool ABILITATO_INVIO = false;
        int TYPE_COMMAND = TypeCommand.EMPTY;

        public MainForm()
        {
            InitializeComponent();
            this.Text = "STTrainingLight V. " + FileVersionInfo.GetVersionInfo(Assembly.GetExecutingAssembly().Location).ProductVersion;
            reset();
            openSerial();
        }

        private void btnStart_Click(object sender, EventArgs e)
        {
            try
            {
                if(STATE_TIMER == StateTimer.STOPPED)
                {
                    btnStart.Text = "STOP";
                    lblCountDown.Visible = true;
                    panelSettings.Enabled = false;
                    COUNTER_COUNT_DOWN = 5;
                    lblCountDown.Text = COUNTER_COUNT_DOWN.ToString(); 
                    timerCountDown.Start();
                    timerCmd.Start();
                    STATE_TIMER = StateTimer.RUNNING;
                }
                else
                {
                    reset();
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine("btnStart_Click: " + ex.Message);
            }
        }

        private void timerCountDown_Tick(object sender, EventArgs e)
        {
            try
            {
                if(COUNTER_COUNT_DOWN == 0)
                {
                    if (timerCountDown.IsRunning)
                        timerCountDown.Stop();
                    DURATA_ALLENAMENTO = (cbDurataAllenamento.SelectedIndex + 1) * 60;
                    DURATA_PAUSA = (cbDurataPausa.SelectedIndex + 1) * 1000;
                    timerSerialAllenamento.Period = 1;
                    //timerSerial.Start();
                    timerDurataAllenamento.Start();           
                }
                else
                {
                    COUNTER_COUNT_DOWN--;
                    lblCountDown.Text = COUNTER_COUNT_DOWN.ToString();
                    TYPE_COMMAND = TypeCommand.COUNTDOWN;
                    ABILITATO_INVIO = true;
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine("timerCountDown_Tick: " + ex.Message);
            }
        }

        private void timerDurataAllenamento_Tick(object sender, EventArgs e)
        {
            try
            {
                if (DURATA_ALLENAMENTO == 0)
                {
                    reset();
                }
                else
                {
                    DURATA_ALLENAMENTO--;
                    double minuti = System.Math.Floor(Convert.ToDouble(DURATA_ALLENAMENTO / 60));
                    double secondi = DURATA_ALLENAMENTO - (minuti * 60);
                    string label = "";
                    if (minuti.ToString().Length == 1)
                        label = "0" + minuti;
                    else
                        label = minuti.ToString();
                    label += ":";
                    if (secondi.ToString().Length == 1)
                        label += "0" + secondi;
                    else
                        label += secondi.ToString();
                    lblCountDown.Text = label;

                }
            }
            catch (Exception ex)
            {
                Console.WriteLine("timerDurataAllenamento_Tick: " + ex.Message);
            }
        }

        private void timerSerialAllenamento_Tick(object sender, EventArgs e)
        {
            try
            {
                timerSerialAllenamento.Period = DURATA_PAUSA;
                TYPE_COMMAND = TypeCommand.TRAINING;
                ABILITATO_INVIO = true;
            }
            catch (Exception ex)
            {
                Console.WriteLine("timerSerialAllenamento_Tick: " + ex.Message);
            }
        }

        private void timerCmd_Tick(object sender, EventArgs e)
        {
            try
            {
                if (ABILITATO_INVIO)
                {
                    if(TYPE_COMMAND == TypeCommand.COUNTDOWN)
                    {
                        serialPortRele.Write(CommandRele.OPEN_ONE);
                        serialPortRele.Write(CommandRele.OPEN_TWO);
                        System.Threading.Thread.Sleep(200);
                        serialPortRele.Write(CommandRele.STOP_ONE);
                        serialPortRele.Write(CommandRele.STOP_TWO);
                    }
                    else if (TYPE_COMMAND == TypeCommand.TRAINING)
                    {
                        //var arr1 = new[] { 1, 2, 3, 4, 5, 6 };
                        //var rndMember = arr1[random.Next(arr1.Length)];
                        serialPortRele.Write(CommandRele.OPEN_ONE);
                        System.Threading.Thread.Sleep(500);
                        serialPortRele.Write(CommandRele.STOP_ONE);
                    }

                    ABILITATO_INVIO = false;
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine("timerCmd_Tick: " + ex.Message);
            }
            
        }

        private void reset()
        {
            try
            {
                btnStart.Text = "START";
                panelSettings.Enabled = true;
                if (timerCountDown.IsRunning)
                    timerCountDown.Stop();
                if (timerDurataAllenamento.IsRunning)
                    timerDurataAllenamento.Stop();
                if (timerSerialAllenamento.IsRunning)
                    timerSerialAllenamento.Stop();
                timerCmd.Stop();
                lblCountDown.Visible = false;
                STATE_TIMER = StateTimer.STOPPED;
                cbDurataAllenamento.SelectedIndex = 0;
                cbDurataPausa.SelectedIndex = 0;
                ABILITATO_INVIO = false;
                TYPE_COMMAND = TypeCommand.EMPTY;
            }
            catch (Exception ex)
            {
                Console.WriteLine("reset: " + ex.Message);
            }
        }

        private void openSerial()
        {
            try
            {
                if (serialPortRele.IsOpen)
                    serialPortRele.Close();
                serialPortRele.Open();
            }
            catch (Exception ex)
            {
                Console.WriteLine("openSerial: " + ex.Message);
            }
        }
        
    }

    public static class StateTimer
    {
        public static int STOPPED = 0;
        public static int RUNNING = 1;
    }

    public class CommandRele
    {
        public static string OPEN_ONE = "6";
        public static string STOP_ONE = "7";
        public static string OPEN_TWO = "8";
        public static string STOP_TWO = "9";
    }

    public static class TypeCommand
    {
        public static int EMPTY = 0;
        public static int COUNTDOWN = 1;
        public static int TRAINING = 2;
    }
}
