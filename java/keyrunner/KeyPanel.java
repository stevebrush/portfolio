package com.keyrunner.gui;


import com.keyrunner.gui.Hero;
import java.awt.*;
import java.awt.event.*;
import javax.swing.*;

public class KeyPanel extends JPanel implements Runnable {

    private static final int PWIDTH = 320; // half the width of iPhone 5
    private static final int PHEIGHT = 568;
    private static final int NUM_DELAYS_PER_YIELD = 16; // record stats every 1 second (roughly)
    private static final int MAX_FRAME_SKIPS = 5; // number of frames with a delay of 0 ms before the animation thread yields to other running threads
    
    private long NPF; // nanoseconds per frame
    
    private boolean running = false; // used to stop the animation thread
    private boolean isPaused = false;
    private boolean gameOver = false;
    
    private Hero hero;
    private Thread animator;
    private Graphics2D graph2D;
    private Image buffer = null;
    
    private boolean leftKeyDown = false;
    private boolean rightKeyDown = false;
    private boolean upKeyDown = false;
    private boolean downKeyDown = false;

    public KeyPanel(long NPF) {

        this.NPF = NPF;

        setBackground(new Color(0x9f, 0xae, 0x74));
        setPreferredSize(new Dimension(PWIDTH, PHEIGHT));
        setFocusable(true);
        requestFocus();
        
        hero = new Hero(PWIDTH, PHEIGHT, NPF);

        addKeyListener(new KeyAdapter() {
            
            @Override
            public void keyPressed(KeyEvent e) {
                processKey(e);
            }

            @Override
            public void keyReleased(KeyEvent e) {
                checkKey(e);
            }
        });

    }

    private void processKey(KeyEvent e) {
        
        int keyCode = e.getKeyCode();
        
        if (!isPaused && !gameOver) {

            if (keyCode == KeyEvent.VK_LEFT) {
                leftKeyDown = true;
                hero.moveLeft();
            }

            if (keyCode == KeyEvent.VK_RIGHT) {
                rightKeyDown = true;
                hero.moveRight();
            }

            if (keyCode == KeyEvent.VK_UP) {
                upKeyDown = true;
                hero.moveUp();
            }

            if (keyCode == KeyEvent.VK_DOWN) {
                downKeyDown = true;
                hero.moveDown();
            }

        }
    }
    
    private void checkKey(KeyEvent e) {
        
        int keyCode = e.getKeyCode();

        if (keyCode == KeyEvent.VK_LEFT) {
            leftKeyDown = false;
            hero.stopLeft();
        }

        if (keyCode == KeyEvent.VK_RIGHT) {
            rightKeyDown = false;
            hero.stopRight();
        }

        if (keyCode == KeyEvent.VK_UP) {
            upKeyDown = false;
            hero.stopUp();
        }

        if (keyCode == KeyEvent.VK_DOWN) {
            downKeyDown = false;
            hero.stopDown();
        }

        // hero only registers "stand still" when no other key is pressed
        if (!leftKeyDown && !rightKeyDown && !upKeyDown && !downKeyDown) {
            hero.standStill();
        }
    }
    
    private void startGame() {
        if (animator == null || !running) {
            animator = new Thread(this); // creates a thread for KeyPanel
            animator.start();
        }
    }

    
    /* Life Cycle Functions */
    
    public void pauseGame() {
        isPaused = true; // called when the JFrame is deactivated / iconified
    }

    public void resumeGame() {
        isPaused = false; // called when the JFrame is activated / deiconified
    }

    public void stopGame() {
        running = false; // called when the JFrame is closing
    }


    /* Rendering and Graphics */
    
    private void gameUpdate() {
        if (!isPaused && !gameOver) {
            // perform computations for the game animations, etc.
            hero.update();
        }
    }

    private void gameRender() {

        if (buffer == null) {
            buffer = createImage(PWIDTH, PHEIGHT); // buffer is as big as the JPanel
            graph2D = (Graphics2D) buffer.getGraphics(); // get buffer's graphics object
        }

        // clear the background
        graph2D.setColor(new Color(0x9f, 0xae, 0x74));
        graph2D.fillRect(0, 0, PWIDTH, PHEIGHT);

        // draw game elements: walls, obstacles, hero
        hero.drawTo(graph2D);

    }

    private void paintScreen() {
        // use active rendering to put the buffered image on-screen
        Graphics g;
        try {
            g = this.getGraphics(); // return JPanel graphics object
            if ((g != null) && (buffer != null)) {
                g.drawImage(buffer, 0, 0, null); // insert buffered image onto JPanel
            }
            g.dispose(); // release graphics object
        } catch (Exception e) {
            System.out.println("Graphics context error: " + e);
        }
    }

    
    /* Implemented Methods */
    
    @Override
    public void addNotify() {
        // wait for the JPanel to be added to the JFrame before starting
        super.addNotify(); // creates the peer
        startGame(); // start the thread
    }

    @Override
    public void run() {

        /* Run() maintains a steady frame rate based on user's machine, and the FPS set in the JFrame */

        long beforeTime, afterTime, timeDiff, sleepTime;
        long overSleepTime = 0L;
        int numDelays = 0;
        long excess = 0L;

        beforeTime = System.nanoTime();

        running = true;

        while (running) {

            gameUpdate();
            gameRender();
            paintScreen();

            afterTime = System.nanoTime();
            timeDiff = afterTime - beforeTime;
            sleepTime = (NPF - timeDiff) - overSleepTime;

            if (sleepTime > 0) {
                // some time left in this cycle
                try {
                    // let the program sleep to let other methods catch up
                    Thread.sleep(sleepTime / 1000000L); // nano -> ms
                }
                
                catch (InterruptedException ex) {
                }

                overSleepTime = (System.nanoTime() - afterTime) - sleepTime; // save how long it took to sleep so we can remove it from the next iteration of sleeping
            } 
            
            else {
                // the frame took longer than the NPF
                excess -= sleepTime; // store excess time value
                overSleepTime = 0L; // reset oversleeptime
                numDelays++;
                if (numDelays >= NUM_DELAYS_PER_YIELD) {
                    Thread.yield(); // give another thread a chance to run
                    numDelays = 0;
                }
            }

            beforeTime = System.nanoTime(); // reset

            /* 
             If frame animation is taking too long, update the game state
             without rendering it, to get the updates/sec nearer to
             the required FPS.
            */
            int skips = 0;
            while ((excess > NPF) && (skips < MAX_FRAME_SKIPS)) {
                excess -= NPF;
                gameUpdate(); // update state but don't render
                skips++; // failsafe so while loop doesn't continue forever
            }
        }

        System.exit(0); // so window disappears
    }
}