# Server/API Tests:
- User Registration / Login
- Data Scraping
- Match Schedule
- Match Details
- Leaderboards Board
- Leaderboards Rank
- Profile

## User Registration
When a new user registers through the iPhone client, it sends a request to our server with a Facebook token which will register the user or log them in, if they already exist.  They also get added to every leaderboard with default value (zero) for each board.
We test this by sending a request internally to our server, like the iPhone would, and check the JSON response to match the structure of success and also check the Redis database to make sure they are in the leaderboard.

## Data Scraping
We have command line commands which scrape data for our database to set everything up. We have about twenty tables in our database that get filled with data from Riot Games API after the scraper is ran. 
We call our scraper command and check for each table if the count of rows in that table is greater than zero which mean the scraper succeeded.

## Match Schedule
We have a API route that retrives the match schedule for the iPhone client. It returns an array of matches that are grouped by the date the match occurs at. 
To test this, we scraped data and then hit our API route and picked a random match from the database to test against the JSON structure returned to verify it works.

## Match Details
Another API route for the iPhone client that returned the match details, which included every game played in a match (best of 5, 3, 1), when it is given a match Id. 
We tested this by selecting a random match from our database and matching that our script was able to fetch the details for that match correctly via a structural check.

## Leaderboards Board
An API route that returned all the users on a given leaderboard from a given start to end with their rank, name, user_id and more. 
Initially we had to create a user and then we could hit the API route and make sure the leaderboards JSON resonse was in fact correct by matching via a structural check.

## Leaderboards Rank
An API route that is similar to the boards but it purely returns a rank (integer) for a desired leaderboard and user_id. 
We created a test user and then made sure the response format matches what we anticipate structurally.

## Profile
The user profile API route that returns all the information about a user: bets, leaderboard positions, stats, and basic user info. 
We create a dummy user and verify the profile returns the correct format structurally in the JSON response.
