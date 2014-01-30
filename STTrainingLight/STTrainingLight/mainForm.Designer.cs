namespace STTrainingLight
{
    partial class MainForm
    {
        /// <summary>
        /// Variabile di progettazione necessaria.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Pulire le risorse in uso.
        /// </summary>
        /// <param name="disposing">ha valore true se le risorse gestite devono essere eliminate, false in caso contrario.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Codice generato da Progettazione Windows Form

        /// <summary>
        /// Metodo necessario per il supporto della finestra di progettazione. Non modificare
        /// il contenuto del metodo con l'editor di codice.
        /// </summary>
        private void InitializeComponent()
        {
            this.components = new System.ComponentModel.Container();
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(MainForm));
            this.timerCountDown = new Multimedia.Timer(this.components);
            this.btnStart = new System.Windows.Forms.Button();
            this.panelTime = new System.Windows.Forms.Panel();
            this.lblCountDown = new System.Windows.Forms.Label();
            this.panelSettings = new System.Windows.Forms.Panel();
            this.label3 = new System.Windows.Forms.Label();
            this.cbDurataPausa = new System.Windows.Forms.ComboBox();
            this.label4 = new System.Windows.Forms.Label();
            this.label2 = new System.Windows.Forms.Label();
            this.cbDurataAllenamento = new System.Windows.Forms.ComboBox();
            this.label1 = new System.Windows.Forms.Label();
            this.timerDurataAllenamento = new Multimedia.Timer(this.components);
            this.timerSerialAllenamento = new Multimedia.Timer(this.components);
            this.timerCmd = new System.Windows.Forms.Timer(this.components);
            this.serialPortRele = new System.IO.Ports.SerialPort(this.components);
            this.panelTime.SuspendLayout();
            this.panelSettings.SuspendLayout();
            this.SuspendLayout();
            // 
            // timerCountDown
            // 
            this.timerCountDown.Mode = Multimedia.TimerMode.Periodic;
            this.timerCountDown.Period = 1000;
            this.timerCountDown.Resolution = 1;
            this.timerCountDown.SynchronizingObject = this;
            this.timerCountDown.Tick += new System.EventHandler(this.timerCountDown_Tick);
            // 
            // btnStart
            // 
            this.btnStart.Font = new System.Drawing.Font("Arial", 48F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.btnStart.Location = new System.Drawing.Point(745, 413);
            this.btnStart.Name = "btnStart";
            this.btnStart.Size = new System.Drawing.Size(251, 137);
            this.btnStart.TabIndex = 0;
            this.btnStart.Text = "START";
            this.btnStart.UseVisualStyleBackColor = true;
            this.btnStart.Click += new System.EventHandler(this.btnStart_Click);
            // 
            // panelTime
            // 
            this.panelTime.Controls.Add(this.lblCountDown);
            this.panelTime.Location = new System.Drawing.Point(13, 13);
            this.panelTime.Name = "panelTime";
            this.panelTime.Size = new System.Drawing.Size(726, 537);
            this.panelTime.TabIndex = 1;
            // 
            // lblCountDown
            // 
            this.lblCountDown.Font = new System.Drawing.Font("Arial", 210F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.lblCountDown.Location = new System.Drawing.Point(-50, 67);
            this.lblCountDown.Name = "lblCountDown";
            this.lblCountDown.Size = new System.Drawing.Size(847, 372);
            this.lblCountDown.TabIndex = 0;
            this.lblCountDown.Text = "00:00";
            this.lblCountDown.TextAlign = System.Drawing.ContentAlignment.MiddleCenter;
            // 
            // panelSettings
            // 
            this.panelSettings.Controls.Add(this.label3);
            this.panelSettings.Controls.Add(this.cbDurataPausa);
            this.panelSettings.Controls.Add(this.label4);
            this.panelSettings.Controls.Add(this.label2);
            this.panelSettings.Controls.Add(this.cbDurataAllenamento);
            this.panelSettings.Controls.Add(this.label1);
            this.panelSettings.Location = new System.Drawing.Point(745, 13);
            this.panelSettings.Name = "panelSettings";
            this.panelSettings.Size = new System.Drawing.Size(251, 394);
            this.panelSettings.TabIndex = 2;
            // 
            // label3
            // 
            this.label3.Font = new System.Drawing.Font("Arial", 18F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label3.Location = new System.Drawing.Point(124, 153);
            this.label3.Name = "label3";
            this.label3.Size = new System.Drawing.Size(124, 31);
            this.label3.TabIndex = 5;
            this.label3.Text = "Secondi";
            this.label3.TextAlign = System.Drawing.ContentAlignment.MiddleCenter;
            // 
            // cbDurataPausa
            // 
            this.cbDurataPausa.DropDownStyle = System.Windows.Forms.ComboBoxStyle.DropDownList;
            this.cbDurataPausa.Font = new System.Drawing.Font("Arial", 18F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.cbDurataPausa.FormattingEnabled = true;
            this.cbDurataPausa.Items.AddRange(new object[] {
            "1",
            "2",
            "3",
            "4",
            "5",
            "6",
            "7",
            "8",
            "9",
            "10"});
            this.cbDurataPausa.Location = new System.Drawing.Point(12, 151);
            this.cbDurataPausa.Name = "cbDurataPausa";
            this.cbDurataPausa.Size = new System.Drawing.Size(81, 37);
            this.cbDurataPausa.TabIndex = 4;
            // 
            // label4
            // 
            this.label4.Font = new System.Drawing.Font("Arial", 18F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label4.Location = new System.Drawing.Point(0, 103);
            this.label4.Name = "label4";
            this.label4.Size = new System.Drawing.Size(251, 31);
            this.label4.TabIndex = 3;
            this.label4.Text = "Durata Pausa";
            this.label4.TextAlign = System.Drawing.ContentAlignment.MiddleCenter;
            // 
            // label2
            // 
            this.label2.Font = new System.Drawing.Font("Arial", 18F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label2.Location = new System.Drawing.Point(124, 50);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(95, 31);
            this.label2.TabIndex = 2;
            this.label2.Text = "Minuti";
            this.label2.TextAlign = System.Drawing.ContentAlignment.MiddleCenter;
            // 
            // cbDurataAllenamento
            // 
            this.cbDurataAllenamento.DropDownStyle = System.Windows.Forms.ComboBoxStyle.DropDownList;
            this.cbDurataAllenamento.Font = new System.Drawing.Font("Arial", 18F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.cbDurataAllenamento.FormattingEnabled = true;
            this.cbDurataAllenamento.Items.AddRange(new object[] {
            "1",
            "2",
            "3",
            "4",
            "5",
            "6",
            "7",
            "8",
            "9",
            "10"});
            this.cbDurataAllenamento.Location = new System.Drawing.Point(12, 48);
            this.cbDurataAllenamento.Name = "cbDurataAllenamento";
            this.cbDurataAllenamento.Size = new System.Drawing.Size(81, 37);
            this.cbDurataAllenamento.TabIndex = 1;
            // 
            // label1
            // 
            this.label1.Font = new System.Drawing.Font("Arial", 18F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.label1.Location = new System.Drawing.Point(0, 0);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(251, 31);
            this.label1.TabIndex = 0;
            this.label1.Text = "Durata Allenamento";
            this.label1.TextAlign = System.Drawing.ContentAlignment.MiddleCenter;
            // 
            // timerDurataAllenamento
            // 
            this.timerDurataAllenamento.Mode = Multimedia.TimerMode.Periodic;
            this.timerDurataAllenamento.Period = 1000;
            this.timerDurataAllenamento.Resolution = 1;
            this.timerDurataAllenamento.SynchronizingObject = this;
            this.timerDurataAllenamento.Tick += new System.EventHandler(this.timerDurataAllenamento_Tick);
            // 
            // timerSerialAllenamento
            // 
            this.timerSerialAllenamento.Mode = Multimedia.TimerMode.Periodic;
            this.timerSerialAllenamento.Period = 1;
            this.timerSerialAllenamento.Resolution = 1;
            this.timerSerialAllenamento.SynchronizingObject = null;
            this.timerSerialAllenamento.Tick += new System.EventHandler(this.timerSerialAllenamento_Tick);
            // 
            // timerCmd
            // 
            this.timerCmd.Interval = 500;
            this.timerCmd.Tick += new System.EventHandler(this.timerCmd_Tick);
            // 
            // serialPortRele
            // 
            this.serialPortRele.BaudRate = 115200;
            this.serialPortRele.ReadTimeout = 500;
            this.serialPortRele.WriteTimeout = 500;
            // 
            // MainForm
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(1008, 562);
            this.Controls.Add(this.panelTime);
            this.Controls.Add(this.panelSettings);
            this.Controls.Add(this.btnStart);
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedSingle;
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.MaximizeBox = false;
            this.MaximumSize = new System.Drawing.Size(1024, 600);
            this.MinimizeBox = false;
            this.MinimumSize = new System.Drawing.Size(1024, 600);
            this.Name = "MainForm";
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen;
            this.Text = "STTrainingLight";
            this.panelTime.ResumeLayout(false);
            this.panelSettings.ResumeLayout(false);
            this.ResumeLayout(false);

        }

        #endregion

        private Multimedia.Timer timerCountDown;
        private System.Windows.Forms.Button btnStart;
        private System.Windows.Forms.Panel panelSettings;
        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.Panel panelTime;
        private System.Windows.Forms.Label label3;
        private System.Windows.Forms.ComboBox cbDurataPausa;
        private System.Windows.Forms.Label label4;
        private System.Windows.Forms.Label label2;
        private System.Windows.Forms.ComboBox cbDurataAllenamento;
        private System.Windows.Forms.Label lblCountDown;
        private Multimedia.Timer timerDurataAllenamento;
        private Multimedia.Timer timerSerialAllenamento;
        private System.Windows.Forms.Timer timerCmd;
        private System.IO.Ports.SerialPort serialPortRele;
    }
}

