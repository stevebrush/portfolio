using UnityEngine;
using System.Collections;
using System.Collections.Generic;

public class RoomBuilder : MonoBehaviour {

	public GameObject room;
	private GameObject[] rooms;
	
	public int currentRoomIndex = 0;
	public int numRoomsMin = 25;
	public int numRoomsMax = 30;

	private int rowWidth;
	private int numTotalRooms;
	private int numTotalPlacements;
	private List<GameObject> placedRooms = new List<GameObject>();
	private bool[] validPlacements;

	private int placementLeft = 0;
	private int placementRight = 0;
	private int placementTop = 0;
	private int placementBottom = 0;

	public GameObject[] BuildRooms() {
		
		/*
		 * 1. Create a random number of rooms.
		 * 2. For each one created, attempt to find a placement on the map arrays next to an available room.
		 * 3. If there is only 1 room in the newly created room's proximity, automatically link the two rooms with a door.
		 * 4. If there is more than 1 room, randomly select one of the rooms to create a link with.
		 * 5. Position the room in the correct spot.
		 * 6. Assign an entrance.
		 * 7. Assign an exit.
		 */

		RoomController roomController;

		int i;

		numTotalRooms = Random.Range (numRoomsMin, numRoomsMax);
		rowWidth = Mathf.FloorToInt (Mathf.Sqrt (numTotalRooms)) + 2;

		numTotalPlacements = rowWidth * rowWidth;
		int placementStart = Mathf.FloorToInt (numTotalPlacements / 2); // Start in the center of the array
		validPlacements = new bool[numTotalPlacements];

		rooms = new GameObject[numTotalRooms];

		// Create the rooms array.
		for (i = 0; i < numTotalRooms; i++) {
			rooms[i] = (GameObject)GameObject.Instantiate (room);
		}

		// Auto-fill the placements array.
		for (i = 0; i < numTotalPlacements; i++) {
			validPlacements [i] = true;
		}

		// Set the placement of the first room.
		roomController = rooms[0].GetComponent<RoomController>();
		roomController.storageIndex = 0;
		roomController.placementIndex = placementStart;
		validPlacements [placementStart] = false;
		placedRooms.Add(rooms[0]);

		// Randomly set the starting position for the other rooms.
		for (i = 1; i < numTotalRooms; i++) {

			roomController = rooms[i].GetComponent<RoomController> ();
			roomController.storageIndex = i;

			AddToMap(roomController, i);

			// Add the room to the placed rooms, so it can receive neighbors.
			placedRooms.Add (rooms[i]);

		}

		// Now let's go through each room and give it a chance to add more doors to adjacent rooms.
		for (int h = 0; h < numTotalRooms; h++) {
			if (Random.value > 0.25f) {
				continue;
			}
			roomController = rooms[h].GetComponent<RoomController> ();
			if (roomController.numDoors > 3) {
				continue;
			}
			LinkRoomAdjacents(roomController);
		}
		
		PositionRooms ();
		return rooms;
	}

	private void AddToMap(RoomController roomController, int i) {
		
		int randomRoomIndex = Random.Range (0, numTotalRooms - 1);
		int counter = 0; // fail safe.
		
		bool findNewRandomRoom = true;

		List<int> possibleDoorIndexes = new List<int>();
		RoomController randomRoomController = GetComponent<RoomController> ();
		
		// Find a room that has an open placement next to it.
		while (findNewRandomRoom && counter < 1000) {
			
			counter++;
			randomRoomIndex = Random.Range (0, placedRooms.Count);

			// Random room cannot be the current room.
			if (randomRoomIndex == i) {
				findNewRandomRoom = true;
				
			} else {

				randomRoomController = placedRooms[randomRoomIndex].GetComponent<RoomController> ();
				possibleDoorIndexes = FindValidDoorsFor (randomRoomController.placementIndex);

				// No doors available, so let's find another room.
				findNewRandomRoom = (possibleDoorIndexes.Count == 0);
			}
		}

		LinkTwoRooms (roomController, randomRoomController, possibleDoorIndexes);

	}

	private void LinkRoomAdjacents (RoomController roomController) {

		int up = Config.UP;
		int right = Config.RIGHT;
		int down = Config.DOWN;
		int left = Config.LEFT;

		int placementIndex = roomController.placementIndex;
		int[] doorIndexes = roomController.doorIndex;
		List<int> validDoorIndexes = new List<int> ();
		RoomController adjacentRoomController = GetComponent<RoomController> ();

		validDoorIndexes = FindValidDoorsFor (placementIndex, false);

		List<int> finalIndexes = new List<int> ();

		// Let's make sure that the valid door indexes aren't already assigned.
		for (int i = 0; i < validDoorIndexes.Count; i++) {
			if (doorIndexes[validDoorIndexes[i]] == -1) {
				finalIndexes.Add (validDoorIndexes[i]);
			}
		}

		if (finalIndexes.Count == 0) {
			return;	
		}

		int randomDoorIndex = Random.Range (0, finalIndexes.Count - 1);
		int randomDoorDirection = finalIndexes [randomDoorIndex];

		switch (randomDoorDirection) {
		case Config.UP:
			adjacentRoomController = GetRoomControllerFromPlacement (placementTop);
			if (adjacentRoomController.numDoors > 3) {
				return;
			}
			roomController.doorIndex[up] = adjacentRoomController.storageIndex;
			adjacentRoomController.doorIndex[down] = roomController.storageIndex;
			break;
		case Config.RIGHT:
			adjacentRoomController = GetRoomControllerFromPlacement (placementRight);
			if (adjacentRoomController.numDoors > 3) {
				return;
			}
			roomController.doorIndex[right] = adjacentRoomController.storageIndex;
			adjacentRoomController.doorIndex[left] = roomController.storageIndex;
			break;
		case Config.DOWN:
			adjacentRoomController = GetRoomControllerFromPlacement (placementBottom);
			if (adjacentRoomController.numDoors > 3) {
				return;
			}
			roomController.doorIndex[down] = adjacentRoomController.storageIndex;
			adjacentRoomController.doorIndex[up] = roomController.storageIndex;
			break;
		case Config.LEFT:
			adjacentRoomController = GetRoomControllerFromPlacement (placementLeft);
			if (adjacentRoomController.numDoors > 3) {
				return;
			}
			roomController.doorIndex[left] = adjacentRoomController.storageIndex;
			adjacentRoomController.doorIndex[right] = roomController.storageIndex;
			break;
		}

		roomController.numDoors++;
		adjacentRoomController.numDoors++;
	}

	private void UpdatePlacementIndexes (int placementIndex) {
		placementLeft = placementIndex - 1;
		placementRight = placementIndex + 1;
		placementTop = placementIndex - rowWidth;
		placementBottom = placementIndex + rowWidth;
	}

	private RoomController GetRoomControllerFromPlacement(int placementIndex) {
		RoomController roomController = GetComponent<RoomController> ();
		for (int i = 0; i < numTotalRooms; i++) {
			roomController = rooms[i].GetComponent<RoomController>();
			if (roomController.placementIndex == placementIndex) {
				break;
			}
		}
		return roomController;
	}

	private List<int> FindValidDoorsFor (int placementIndex, bool isVacant = true) {

		List<int> possibleDoorIndexes = new List<int>();
		
		// Store the array indexes of the possible room locations.
		UpdatePlacementIndexes (placementIndex);
		
		// Make sure the new placement isn't out of bounds.
		bool leftNull = ((placementIndex > 0 && placementIndex % rowWidth == 0) || placementLeft < 0); // if there are no remainders on the current room, we're at the start of the array; or, the start of the array.
		bool rightNull = ((placementRight) % rowWidth == 0 || placementRight > numTotalPlacements); // there are no remainders, so it's the start of a new row; or, the end of the array.
		bool topNull = (placementTop < 0);
		bool bottomNull = (placementBottom > (numTotalPlacements - 1));

		// Top side available?
		if (!topNull && validPlacements[placementTop] == isVacant) {
			possibleDoorIndexes.Add (Config.UP);
		}

		// Right side available?
		if (!rightNull && validPlacements[placementRight] == isVacant) {
			possibleDoorIndexes.Add (Config.RIGHT);
		}

		// Bottom side available?
		if (!bottomNull && validPlacements[placementBottom] == isVacant) {
			possibleDoorIndexes.Add (Config.DOWN);
		}

		// Left side available?
		if (!leftNull && validPlacements[placementLeft] == isVacant) {
			possibleDoorIndexes.Add (Config.LEFT);
		}

		return possibleDoorIndexes;
	}

	private void LinkTwoRooms(RoomController targetRoom, RoomController randomRoom, List<int> possibleDoorIndexes) {

		int randomIndex = Random.Range (0, possibleDoorIndexes.Count);
		int randomDoorIndex = possibleDoorIndexes[randomIndex];
		
		// Assign both rooms the appropriate door indexes.
		switch (randomDoorIndex) {
		case Config.UP:
			validPlacements[placementTop] = false;
			targetRoom.placementIndex = placementTop;
			targetRoom.doorIndex[Config.DOWN] = randomRoom.storageIndex;
			randomRoom.doorIndex[Config.UP] = targetRoom.storageIndex;
			break;
		case Config.RIGHT:
			validPlacements[placementRight] = false;
			targetRoom.placementIndex = placementRight;
			targetRoom.doorIndex[Config.LEFT] = randomRoom.storageIndex;
			randomRoom.doorIndex[Config.RIGHT] = targetRoom.storageIndex;
			break;
		case Config.DOWN:
			validPlacements[placementBottom] = false;
			targetRoom.placementIndex = placementBottom;
			targetRoom.doorIndex[Config.UP] = randomRoom.storageIndex;
			randomRoom.doorIndex[Config.DOWN] = targetRoom.storageIndex;
			break;
		case Config.LEFT:
			validPlacements[placementLeft] = false;
			targetRoom.placementIndex = placementLeft;
			targetRoom.doorIndex[Config.RIGHT] = randomRoom.storageIndex;
			randomRoom.doorIndex[Config.LEFT] = targetRoom.storageIndex;
			break;
		}
		
		// Update the number of rooms so we can elect an appropriate sprite.
		targetRoom.numDoors++;
		randomRoom.numDoors++;
	}

	private void PositionRooms() {
		int i;
		int length = rooms.Length;
		RoomController roomController;
		for (i = 0; i < length; i++) {
			roomController = rooms[i].GetComponent<RoomController> ();
			rooms[i].transform.position = PositionFromPlacement(roomController.placementIndex);
			roomController.SetSpriteMap();
		}
	}
	
	private Vector2 PositionFromPlacement(int placement) {
		int x; // 0-6
		int y; // 0-6
		if (placement >= rowWidth) {
			y = Mathf.FloorToInt (placement / rowWidth);
			x = placement - (y * rowWidth);
		} else {
			y = 0;
			x = placement;
		}
		return new Vector2(x, -y);
	}
}
