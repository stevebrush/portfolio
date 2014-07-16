package com.keyrunner.gui;


import com.keyrunner.gui.KeyPanel;
import java.awt.event.*;
import javax.swing.*;

public class KeyRunner extends JFrame implements WindowListener {

    
    private static int DEFAULT_FPS = 80;
    private long NPF; // nanoseconds per frame
    private KeyPanel gamePanel;
    
    public KeyRunner () {
        NPF = (long) (1000.0/DEFAULT_FPS)*1000000L; // get how many nanoseconds per frame should elapse to maintain 80 FPS
        createGUI();
        addWindowListener(this);
        setResizable(false);
        setVisible(true);
        pack();
        setLocationRelativeTo(null);
        setDefaultCloseOperation(DISPOSE_ON_CLOSE);
    }
    
    private void createGUI () {
        gamePanel = new KeyPanel(NPF); // send NPF to jpanel
        getContentPane().add(gamePanel, "Center");
    }
    
    public static void main(String[] args) {
        new KeyRunner(); 
    }
    
    
    @Override
    public void windowOpened(WindowEvent e) {}

    @Override
    public void windowClosing(WindowEvent e) {
        gamePanel.stopGame();
    }

    @Override
    public void windowClosed(WindowEvent e) {}

    @Override
    public void windowIconified(WindowEvent e) {
        gamePanel.pauseGame();
    }

    @Override
    public void windowDeiconified(WindowEvent e) {
        gamePanel.resumeGame();
    }

    @Override
    public void windowActivated(WindowEvent e) {
        gamePanel.resumeGame();
    }

    @Override
    public void windowDeactivated(WindowEvent e) {
        gamePanel.pauseGame();
    }

}