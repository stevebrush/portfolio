using UnityEngine;
using System.Collections;
using System.Collections.Generic;

public class ContainerBuilder : MonoBehaviour {

	public GameObject container;
	private List<GameObject> containers;
	public int numContainersPerRoomMin = 1;
	public int numContainersPerRoomMax = 3;

	public List<GameObject> BuildContainers (GameObject[] rooms) {

		int i = 0;
		int j;
		int numRooms = rooms.Length;
		int numContainersThisRoom;
		List<GameObject> roomContainers = new List<GameObject> ();

		GameObject room;
		int roomWidth;
		int roomHalf;
		int randomX = 0;
		int randomY = 0;
		Texture2D texture;
		Sprite sprite;
		Rect rect;
		Color pixel;
		bool findPosition;

		containers = new List<GameObject> ();


		for (; i < numRooms; i++) {



			room = rooms[i];

			/*
			Vector2 position = room.transform.position;
			Vector2 size = GetComponent<SpriteRenderer> ().bounds.size;
			float half = size.x / 2;
			float minX = position.x - half;
			float maxX = position.x + half;
			int randomX = Random.Range (minX, maxX);
			int randomY = 0.0f;

			Debug.Log (room.GetComponent<RoomController>().storageIndex + ", " + randomX);
			*/




			sprite = room.GetComponent<SpriteRenderer> ().sprite;
			rect = sprite.textureRect;
			roomWidth = (int) rect.width;
			roomHalf = Mathf.FloorToInt (roomWidth / 2);


			//Debug.Log (sprite.bounds.);

			//Debug.Log (Random.Range (textureBounds.x, textureBounds.xMax));
			//
			//Debug.Log (room.transform.position);
			//Vector2 point = Rect.PointToNormalized(rect, new Vector2(randomX, randomY));
			//Debug.Log (textureBounds.position + ", " + point);
			//Debug.Log ("New x: " + (textureBounds.position.x + point.x));
			//Debug.Log ("New y: " + (textureBounds.position.y + point.y));



			// Reset the room's container list.
			roomContainers.Clear ();
			numContainersThisRoom = Random.Range (numContainersPerRoomMin, numContainersPerRoomMax);
			for (j = 0; j < numContainersThisRoom; j++) {
				findPosition = true;
				int counter = 0;
				while (findPosition && counter < 1000) {
					
					randomX = Random.Range (1, roomWidth);
					randomY = Random.Range (1, roomWidth);
					
					pixel = sprite.texture.GetPixel (randomX, randomY);
					
					if (pixel.a != 0.0f) {
						findPosition = false;
					}
					
					counter++;
				}
				
				if (counter >= 1000) {
					Debug.Log ("COUNTER WAS REACHED!");
				} else {
					Debug.Log ("Pixel found here: " + randomX + ", " + randomY);
				}
				
				float coordinateX = ((randomX - roomHalf) / 100f);
				float coordinateY = ((randomY - roomHalf) / 100f);

				// Set up the new container.
				GameObject newContainer = (GameObject)GameObject.Instantiate (container);
				ContainerController containerController = newContainer.GetComponent<ContainerController>();
				containerController.room = rooms[i];
				newContainer.transform.parent = room.transform;
				newContainer.transform.localPosition = new Vector2(coordinateX, coordinateY);

				containers.Add (newContainer);
				roomContainers.Add (newContainer);

			}

			room.GetComponent<RoomController> ().SetContainers (roomContainers);

		}

		return containers;
	}

	public List<GameObject> BuildContainers (GameObject[] rooms, GameObject[] quadrants) {

		int i;
		int k;
		int j;
		int numContainersPerRoom;
		int numQuadrants = quadrants.Length;
		int numRooms = rooms.Length;
		int numSectorsPerQuadrant = quadrants [0].transform.childCount;
		int numTotalSectors = numSectorsPerQuadrant * numQuadrants;
		List<int> availableSectors = new List<int>();

		containers = new List<GameObject> ();
		List<GameObject> roomContainers = new List<GameObject> ();

		GameObject newContainer;
		ContainerController newContainerScript;

		int randomIndex;
		int randomSectorIndex;
		int randomQuadrantIndex;

		// Loop through each room...
		for (i = 0; i < numRooms; i++) {

			// Reset the room's container list.
			roomContainers.Clear ();

			// Prepopulate available placements
			availableSectors.Clear ();
			for (k = 0; k < numTotalSectors; k++) {
				availableSectors.Add(k);
			}

			numContainersPerRoom = Random.Range (numContainersPerRoomMin, numContainersPerRoomMax);

			// Loop through each container...
			for (j = 0; j < numContainersPerRoom; j++) {

				randomQuadrantIndex = Random.Range (0, numQuadrants); // 0-3
				randomIndex = Random.Range(0, availableSectors.Count - 1);
				availableSectors.RemoveAt(randomIndex);
				randomSectorIndex = availableSectors[randomIndex];

				// The sector index is part of a large array, but only numbers 1-9 are used.
				if (randomSectorIndex > numSectorsPerQuadrant - 1) { // 9 = 0, 10 = 1, etc.
					randomSectorIndex = randomSectorIndex - (Mathf.FloorToInt (randomSectorIndex / numSectorsPerQuadrant) * numSectorsPerQuadrant);
				}

				// Set up the new container.
				newContainer = (GameObject)GameObject.Instantiate (container);
				newContainerScript = newContainer.GetComponent<ContainerController>();
				newContainerScript.room = rooms[i];
				newContainerScript.sector = quadrants[randomQuadrantIndex].transform.GetChild(randomSectorIndex).gameObject;
				newContainer.transform.parent = Camera.main.transform;

				containers.Add (newContainer);
				roomContainers.Add (newContainer);
			}

			rooms[i].GetComponent<RoomController>().SetContainers(roomContainers);
		}

		return containers;
	}
}
