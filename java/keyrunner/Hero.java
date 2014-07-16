package com.keyrunner.gui;


import java.awt.Graphics;
import java.awt.Image;
import java.awt.image.BufferedImage;
import java.awt.image.ImageObserver;
import java.awt.image.ImageProducer;
import java.io.IOException;
import javax.imageio.ImageIO;

public class Hero extends Image {

    private int height = 65;
    private int width = 30;
    private int rows = 3;
    private int cols = 12;
    private int heroX = 40;
    private int heroY = 40;
    private int increment = 5; // how many pixels to move
    private final int NUM_FRAMES = 12;
    
    private int pw; // jpanel dimensions
    private int ph;
    
    private long NPF;
    private double animDuration = 0.5; // seconds
    private long animTotalTime = 0L;
    private double animIteration;
    
    private boolean movingLeft = false;
    private boolean movingRight = false;
    private boolean movingUp = false;
    private boolean movingDown = false;
    private boolean standingStill = true;
    private boolean doFlipHoriz = false;
    
    private BufferedImage sheet;
    private BufferedImage[] sprites;
    private int spriteIndex = 0;

    public Hero(int pW, int pH, long npf) {
        pw = pW;
        ph = pH;
        this.NPF = npf;
        animIteration = (animDuration * 1000000000) / NUM_FRAMES;
        System.out.println("NPF: " + NPF);
        System.out.println("animIteration: " + animIteration);
        create();
    }

    private void create() {
        try {
            sheet = ImageIO.read(getClass().getResource("images/runner.png"));
            sprites = new BufferedImage[rows * cols];
            for (int i = 0; i < rows; i++) {
                for (int j = 0; j < cols; j++) {
                    sprites[(i * cols) + j] = sheet.getSubimage(j * width, i * height, width, height);
                }
            }
        } catch (IOException error) {
            System.out.println(error.getMessage());
        }

    }

    public void moveLeft() {
        movingLeft = true;
        movingRight = false;
        standingStill = false;
        doFlipHoriz = true;
    }
    
    public void stopLeft() {
        movingLeft = false;
    }

    public void moveRight() {
        movingRight = true;
        movingLeft = false;
        standingStill = false;
        doFlipHoriz = false;
    }
    
    public void stopRight() {
        movingRight = false;
    }

    public void moveUp() {
        movingUp = true;
        movingDown = false;
        standingStill = false;
        doFlipHoriz = false;
    }
    
    public void stopUp() {
        movingUp = false;
    }

    public void moveDown() {
        movingDown = true;
        movingUp = false;
        standingStill = false;
        doFlipHoriz = false;
    }
    
    public void stopDown() {
        movingDown = false;
    }
    
    public void standStill() {
        standingStill = true;
        movingLeft = movingRight = movingUp = movingDown = false;
        doFlipHoriz = false;
    }

    public void update() {
        
        animTotalTime += NPF;
        
        if (animTotalTime >= animIteration) {
        
            // x,y
            if (movingLeft) {
                heroX -= increment;
                if (heroX < 0) {
                    heroX = 0;
                }
            }

            if (movingRight) {
                heroX += increment;
                if (heroX > pw-width) {
                    heroX = pw-width;
                }
            }

            if (movingUp) {
                heroY -= increment;
                if (heroY < 0) {
                    heroY = 0;
                }
            }

            if (movingDown) {
                heroY += increment;
                if (heroY > ph-height) {
                    heroY = ph-height;
                }
            }


            // sprite indexing
            if (standingStill) {
                spriteIndex = 0;
            }
            
            if (movingUp) {
                spriteIndex++;
                if (spriteIndex < (cols * (rows - 1))) {
                    spriteIndex = cols * (rows - 1);
                }
                if (spriteIndex >= (cols * rows)) {
                    spriteIndex = cols * (rows - 1);
                }
            }

            else if (movingDown) {
                spriteIndex++;
                if (spriteIndex > (cols - 1)) {
                    spriteIndex = 0;
                }
            } 

            else if (movingLeft) {
                spriteIndex++;
                if (spriteIndex < cols) {
                    spriteIndex = cols;
                }
                if (spriteIndex >= cols * 2) {
                    spriteIndex = cols;
                }
            }

            else if (movingRight) {
                spriteIndex++;
                if (spriteIndex < cols) {
                    spriteIndex = cols;
                }
                if (spriteIndex >= cols * 2) {
                    spriteIndex = cols;
                }
            }
            
            animTotalTime = 0;
        }
        
    }

    public void drawTo(Graphics g) {
        if (sprites.length > spriteIndex) {
            Image img = sprites[spriteIndex];
            if (doFlipHoriz) {
                g.drawImage(img, img.getWidth(null) + heroX, heroY, heroX, img.getHeight(null) + heroY, 0, 0, img.getWidth(null), img.getHeight(null), null);
            } else {
                g.drawImage(img, heroX, heroY, null);
            }
        }
    }

    @Override
    public int getWidth(ImageObserver io) {
        throw new UnsupportedOperationException("Not supported yet.");
    }

    @Override
    public int getHeight(ImageObserver io) {
        throw new UnsupportedOperationException("Not supported yet.");
    }

    @Override
    public ImageProducer getSource() {
        throw new UnsupportedOperationException("Not supported yet.");
    }

    @Override
    public Graphics getGraphics() {
        throw new UnsupportedOperationException("Not supported yet.");
    }

    @Override
    public Object getProperty(String string, ImageObserver io) {
        throw new UnsupportedOperationException("Not supported yet.");
    }
}