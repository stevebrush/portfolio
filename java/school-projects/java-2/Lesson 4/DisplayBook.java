/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 July 3.
 * Chapter 13, Exercise # 3.
 * The DisplayBook application displays the user's favorite book, or creates
 * the book (file) if the file is not found.
 */
import java.nio.file.*;
import java.io.*;
import static java.nio.file.StandardOpenOption.*;
import java.util.*;
public class DisplayBook
{
    public static void main(String[] args)
    {
        Path file = Paths.get("FavoriteBook.txt");
        InputStream input = null;
        boolean bookExists = false;
        String favoriteBook = "";
        while (!bookExists)
        {
            try
            {
                // Attempt to locate the favorite book text file.
                input = Files.newInputStream(file);
                BufferedReader reader = new BufferedReader(new InputStreamReader(input));
                favoriteBook = reader.readLine();
                input.close();
                bookExists = true;

                // Display the user's favorite book:
                System.out.println("Your favorite book is: " + favoriteBook);
            }
            catch (IOException error)
            {
                // The book was not found, so let's ask the user to supply it.
                Scanner scanner = new Scanner(System.in);
                System.out.print("Favorite book was not found. Please enter your favorite book >>");
                favoriteBook = scanner.nextLine();
                byte[] data = favoriteBook.getBytes();
                try
                {
                    // Attempt to create the file.
                    OutputStream output = new BufferedOutputStream(Files.newOutputStream(file, CREATE));
                    output.write(data);
                    output.flush();
                    output.close();
                }
                catch (Exception e)
                {
                    // The file could not be created.
                    System.out.println("Critical Error. Cannot write to file! " + e);
                    bookExists = true;
                }
            }
        }
    }
}
