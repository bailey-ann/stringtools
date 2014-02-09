/*
 * 09/02/2014 versione librerie 6.8.3
 */

using MySql.Data.MySqlClient;
using System;
using System.Collections.Generic;
using System.Data;
using System.Linq;
using System.Text;

namespace NapaUtilsNet
{
    public class NapaMySql
    {
        private MySqlConnection connection;
        private string server;
        private string port;
        private string database;
        private string uid;
        private string password;

        public NapaMySql(string aServer, string aPort, string aDatabase, string aUid, string aPassword)
        {
            server = aServer;
            port = aPort;
            database = aDatabase;
            uid = aUid;
            password = aPassword;
            Initialize();
            
        }

        private void Initialize()
        {
            try
            {               
                string connectionString;
                connectionString = "SERVER=" + server + ";" + "DATABASE=" +
                database + ";" + "UID=" + uid + ";" + "PASSWORD=" + password + ";";
                connection = new MySqlConnection(connectionString);
            }
            catch (Exception e)
            {
                Console.WriteLine(e.Message);
            }            
        }

        public bool OpenConnection()
        {
            try
            {
                connection.Open();
                return true;
            }
            catch (MySqlException ex)
            {
                //When handling errors, you can your application's response based 
                //on the error number.
                //The two most common error numbers when connecting are as follows:
                //0: Cannot connect to server.
                //1045: Invalid user name and/or password.
                switch (ex.Number)
                {
                    case 0:
                        Console.WriteLine("Cannot connect to server.  Contact administrator");
                        break;

                    case 1045:
                        Console.WriteLine("Invalid username/password, please try again");
                        break;
                }
                return false;
            }
        }

        public bool CloseConnection()
        {
            try
            {
                connection.Close();
                return true;
            }
            catch (MySqlException eSql)
            {
                Console.WriteLine(eSql.Message);
                return false;
            }
            catch (Exception e)
            {
                Console.WriteLine(e.Message);
                return false;
            }
        }

        public bool IsOpen()
        {
            return this.connection.State.Equals(ConnectionState.Open);
        }

        public int QueryForInsert(string query)
        {
            try
            {
                if (!IsOpen())
                    OpenConnection();

                MySqlCommand cmd = new MySqlCommand(query, connection);

                return cmd.ExecuteNonQuery();

            }
            catch (MySqlException eSql)
            {
                Console.WriteLine(eSql.Message);
                return 0;
            }
            catch (Exception e)
            {
                Console.WriteLine(e.Message);
                return 0;
            }
        }

        public int QueryForUpdate(string query)
        {
            try
            {
                if (!IsOpen())
                    OpenConnection();

                MySqlCommand cmd = new MySqlCommand();
                //Assign the query using CommandText
                cmd.CommandText = query;
                //Assign the connection using Connection
                cmd.Connection = connection;

                //Execute query
                return cmd.ExecuteNonQuery();

            }
            catch (MySqlException eSql)
            {
                Console.WriteLine(eSql.Message);
                return 0;
            }
            catch (Exception e)
            {
                Console.WriteLine(e.Message);
                return 0;
            }
        }

        public int QueryForDelete(string query)
        {
            try
            {
                if (!IsOpen())
                    OpenConnection();

                MySqlCommand cmd = new MySqlCommand(query, connection);

                return cmd.ExecuteNonQuery();

            }
            catch (MySqlException eSql)
            {
                Console.WriteLine(eSql.Message);
                return 0;
            }
            catch (Exception e)
            {
                Console.WriteLine(e.Message);
                return 0;
            }
        }

        public int QueryForInt(string query)
        {
            int returnValue = 0;
            try
            {
                if (!IsOpen())
                    OpenConnection();

                MySqlCommand cmd = new MySqlCommand(query, connection);

                object value = cmd.ExecuteScalar();

                if (value != null)
                {
                    returnValue = int.Parse(value + "");
                }

                return returnValue;
            }
            catch (MySqlException eSql)
            {
                Console.WriteLine(eSql.Message);
                return 0;
            }
            catch (Exception e)
            {
                Console.WriteLine(e.Message);
                return 0;
            }
        }

        public string QueryForString(string query)
        {
            string returnValue = "";
            try
            {
                if (!IsOpen())
                    OpenConnection();

                MySqlCommand cmd = new MySqlCommand(query, connection);

                object value = cmd.ExecuteScalar();

                if (value != null)
                {
                    returnValue = value.ToString();
                }

                return returnValue;
            }
            catch (MySqlException eSql)
            {
                Console.WriteLine(eSql.Message);
                return "";
            }
            catch (Exception e)
            {
                Console.WriteLine(e.Message);
                return "";
            }
        }

        public float QueryForFloat(string query)
        {
            float returnValue = 0;
            try
            {
                if (!IsOpen())
                    OpenConnection();

                MySqlCommand cmd = new MySqlCommand(query, connection);

                object value = cmd.ExecuteScalar();

                if (value != null)
                {
                    returnValue = (float)value;
                }

                return returnValue;
            }
            catch (MySqlException eSql)
            {
                Console.WriteLine(eSql.Message);
                return 0;
            }
            catch (Exception e)
            {
                Console.WriteLine(e.Message);
                return 0;
            }
        }

        public double QueryForDouble(string query)
        {
            double returnValue = 0;
            try
            {
                if (!IsOpen())
                    OpenConnection();

                MySqlCommand cmd = new MySqlCommand(query, connection);

                object value = cmd.ExecuteScalar();

                if (value != null)
                {
                    returnValue = (double)value;
                }

                return returnValue;
            }
            catch (MySqlException eSql)
            {
                Console.WriteLine(eSql.Message);
                return 0;
            }
            catch (Exception e)
            {
                Console.WriteLine(e.Message);
                return 0;
            }
        }

        public decimal QueryForDecimal(string query)
        {
            decimal returnValue = 0;
            try
            {
                if (!IsOpen())
                    OpenConnection();

                MySqlCommand cmd = new MySqlCommand(query, connection);

                object value = cmd.ExecuteScalar();

                if (value != null)
                {
                    returnValue = (decimal)value;
                }

                return returnValue;
            }
            catch (MySqlException eSql)
            {
                Console.WriteLine(eSql.Message);
                return 0;
            }
            catch (Exception e)
            {
                Console.WriteLine(e.Message);
                return 0;
            }
        }

        public bool QueryForBoolean(string query)
        {
            bool returnValue = false;
            try
            {
                if (!IsOpen())
                    OpenConnection();

                MySqlCommand cmd = new MySqlCommand(query, connection);

                object value = cmd.ExecuteScalar();

                if (value != null)
                {
                    returnValue = (bool)value;
                }

                return returnValue;
            }
            catch (MySqlException eSql)
            {
                Console.WriteLine(eSql.Message);
                return false;
            }
            catch (Exception e)
            {
                Console.WriteLine(e.Message);
                return false;
            }
        }

        public List<object> QueryForSingleObjectList(string query, int numeroCampi)
        {
            List<object> returnValue = new List<object>();
            try
            {
                if (!IsOpen())
                    OpenConnection();

                MySqlCommand cmd = new MySqlCommand(query, connection);

                MySqlDataReader dataReader = cmd.ExecuteReader();

                while (dataReader.Read())
                {
                    for (int i = 0; i < numeroCampi; i++)
                    {
                        if (!DBNull.Value.Equals(dataReader.GetValue(i)))
                            returnValue.Add(dataReader.GetValue(i));
                        else
                            returnValue.Add(null);
                    }
                        
                }

                dataReader.Close();

                return returnValue;
            }
            catch (MySqlException eSql)
            {
                Console.WriteLine(eSql.Message);
                return null;
            }
            catch (Exception e)
            {
                Console.WriteLine(e.Message);
                return null;
            }
        }

        public List<List<object>> QueryForMultipleObjectList(string query, int numeroCampi)
        {
            List<List<object>> returnValue = new List<List<object>>();
            try
            {
                if (!IsOpen())
                    OpenConnection();

                MySqlCommand cmd = new MySqlCommand(query, connection);

                MySqlDataReader dataReader = cmd.ExecuteReader();

                while (dataReader.Read())
                {
                    List<object> singleList = new List<object>();
                    for (int i = 0; i < numeroCampi; i++)
                    {
                        if (!DBNull.Value.Equals(dataReader.GetValue(i)))
                            singleList.Add(dataReader.GetValue(i));
                        else
                            singleList.Add(null);
                    }
                    returnValue.Add(singleList);
                }

                dataReader.Close();

                return returnValue;
            }
            catch (MySqlException eSql)
            {
                Console.WriteLine(eSql.Message);
                return null;
            }
            catch (Exception e)
            {
                Console.WriteLine(e.Message);
                return null;
            }
        }
    }
}
