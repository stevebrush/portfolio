/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 July 3.
 * Chapter 13, Exercise # 1.
 * The FileStatistics application displays various information about a text file.
 */
import java.nio.file.*;
import java.nio.file.attribute.*;
import java.io.IOException;
public class FileStatistics
{
    public static void main(String[] args)
    {
        Path filePath = Paths.get("FileStatistics_file.txt");
        try
        {
            BasicFileAttributes attr = Files.readAttributes(filePath, BasicFileAttributes.class);
            System.out.println("----------------------------------------------------------");
            System.out.println("File Information");
            System.out.println("----------------------------------------------------------");
            System.out.println("Name: " + filePath.getFileName().toString());
            System.out.println("Size: " + attr.size() + " bytes");
            System.out.println("Last Modified: " + attr.lastModifiedTime());
            System.out.println("----------------------------------------------------------");
        }
        catch (IOException error)
        {
            System.out.println("IO Exception: " + error.getMessage());
        }
    }
}
