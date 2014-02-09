using System;
using System.Collections.Generic;
using System.IO.Ports;
using System.Linq;
using System.Text;

namespace NapaUtilsNet
{
    public delegate void RicezioneDatoHandler(string dato);

    public class NapaSerialPort
    {

        public event RicezioneDatoHandler RicezioneDatoEvent;

        public enum TransmissionType { Text, Hex }

        private SerialPort comPort = new SerialPort();
        private string baudRate = string.Empty;
        private string parity = string.Empty;
        private string stopBits = string.Empty;
        private string dataBits = string.Empty;
        private string portName = string.Empty;
        private TransmissionType currentTransmissionType;

        public NapaSerialPort(string aPortName, string aBaudRate, string aParity, string aStopBits, string aDataBits, TransmissionType aCurrentTransmissionType)
        {
            baudRate = aPortName;
            baudRate = aBaudRate;
            parity = aParity;
            stopBits = aStopBits;
            dataBits = aDataBits;
            currentTransmissionType = aCurrentTransmissionType;

            Initialize(); 
        }

        private void Initialize()
        {
            try
            {
                comPort.DataReceived += new SerialDataReceivedEventHandler(comPort_DataReceived);
                OpenPort();
            }
            catch (Exception e)
            {
                Console.WriteLine(e.Message);
            }
        }

        private bool OpenPort()
        {
            try
            {
                if (comPort.IsOpen == true) comPort.Close();

                comPort.BaudRate = int.Parse(baudRate);
                comPort.DataBits = int.Parse(dataBits);
                comPort.StopBits = (StopBits)Enum.Parse(typeof(StopBits), stopBits);
                comPort.Parity = (Parity)Enum.Parse(typeof(Parity), parity);
                comPort.PortName = portName;
                comPort.Open();
                return true;
            }
            catch (Exception ex)
            {
                Console.WriteLine(ex.Message);
                return false;
            }
        }

        private void comPort_DataReceived(object sender, SerialDataReceivedEventArgs e)
        {
            switch (currentTransmissionType)
            {
                case TransmissionType.Text:
                    string msg = comPort.ReadExisting();
                    OnRicezioneDatoEvent(msg);
                    break;
                case TransmissionType.Hex:
                    int bytes = comPort.BytesToRead;
                    byte[] comBuffer = new byte[bytes];
                    comPort.Read(comBuffer, 0, bytes);
                    OnRicezioneDatoEvent(NapaFormat.ByteToHex(comBuffer));
                    break;
                default:
                    string str = comPort.ReadExisting();
                    OnRicezioneDatoEvent(str);
                    break;
            }
        }

        public void WriteData(string msg)
        {
            switch (currentTransmissionType)
            {
                case TransmissionType.Text:
                    if (!(comPort.IsOpen == true)) 
                        comPort.Open();
                    comPort.Write(msg);
                    break;
                case TransmissionType.Hex:
                    try
                    {
                        byte[] newMsg = NapaFormat.HexToByte(msg);
                        comPort.Write(newMsg, 0, newMsg.Length);
                        //DisplayData(MessageType.Outgoing, ByteToHex(newMsg) + "\n");
                    }
                    catch (FormatException ex)
                    {
                        Console.WriteLine(ex.Message);
                    }
                    break;
                default:
                    if (!(comPort.IsOpen == true)) 
                        comPort.Open();
                    comPort.Write(msg);
                    break;
            }
        }

        protected virtual void OnRicezioneDatoEvent(string dato)
        {
            if (RicezioneDatoEvent != null)
            {
                RicezioneDatoEvent(dato);
            }
        }
    }
}
