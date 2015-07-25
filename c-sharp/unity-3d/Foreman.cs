using UnityEngine;
using System.Collections;
using System.Collections.Generic;


public class Foreman : MonoBehaviour {

	// Events
	public delegate void ForemanEvent ();
	public static event ForemanEvent OnBeforeUpdate;
	public static event ForemanEvent OnAfterUpdate;

	// Camera
	public Camera mainCamera;
	private PositionCamera cameraScript;

	// Room
	private int currentRoomIndex;
	private GameObject[] rooms;
	private GameObject room;
	private RoomController roomScript;

	// Containers
	public GameObject container;
	private Collider2D[] containerColliders;
	private int numContainerColliders;
	private int containerActiveLayer;
	private float searchRadius = 0.15f;

	// Quadrants
	private GameObject[] quadrants;
	private Collider2D[] sectorColliders = new Collider2D[36];
	private int numSectorColliders;

	// Is the foreman ready to accept actions?
	private bool isReady = false;

	void Start () {

		/*
		 * 1. Build the quadrants
		 * 2. Build the rooms
		 * 3. Build the containers
		 * 4. Set the current room
		 * 5. Force the rooms to subscribe to events
		 * 6. Fire events.
		 * */


		// Presets.
		containerActiveLayer = 1 << LayerMask.NameToLayer ("ActiveItems");
		
		// Set the camera.
		cameraScript = mainCamera.GetComponent<PositionCamera> ();
		
		// Build the rooms.
		RoomBuilder floorBuilderScript = GetComponent<RoomBuilder> ();
		rooms = floorBuilderScript.BuildRooms();
		
		// Add in containers.
		ContainerBuilder containerBuilder = GetComponent<ContainerBuilder> ();
		containerBuilder.BuildContainers (rooms);
		
		// Activate the current room.
		SetRoom (floorBuilderScript.currentRoomIndex);
		
		// Move the camera, and action!
		cameraScript.SnapTo (rooms[currentRoomIndex].transform);
		Subscribe ();
	}

	void OnDisable() {
		Unsubscribe ();
	}

	private void ActivateForeman() {
		Debug.Log ("ActivateForeman();");
		isReady = true;
	}
	
	private void DeactivateForeman() {
		Debug.Log ("DeactivateForeman();");
		isReady = false;
	}
	
	private bool ForemanReady() {
		return isReady;
	}

	private void SearchWithTap(Touch t) {
		//Debug.Log("Screen tapped, searching... " + t.position);
	}

	private void MoveMap(string direction) {

		// The foreman isn't accepting new commands at the moment.
		if (!ForemanReady()) {
			return;
		}

		int doorIndex = 0;
		int nextRoomIndex;

		// Determine the next room's door index (0, 1, 2, 3).
		switch (direction) {
		case "down":
			doorIndex = Config.DOWN;
			break;
		case "left":
			doorIndex = Config.LEFT;
			break;
		case "up":
			doorIndex = Config.UP;
			break;
		case "right":
			doorIndex = Config.RIGHT;
			break;
		}

		nextRoomIndex = roomScript.doorIndex [doorIndex];

		if (nextRoomIndex != -1) {

			RoomController nextRoomScript = rooms[nextRoomIndex].GetComponent<RoomController>();

			// Is the next room locked?
			if (nextRoomScript.locked) {
				return;
			}

			roomScript.visited = true;

			// Run the before update event.
			if (OnBeforeUpdate != null) {
				OnBeforeUpdate();
			}

			// Demote current room.
			// Set the current room to visited.
			roomScript.OnReady -= ActivateForeman;
			roomScript.selected = false;

			// Update the next room's status.
			SetRoom(nextRoomIndex);

			// Move the camera to the next room.
			if (nextRoomScript.visited == true) {
				cameraScript.MoveTo (GetCurrentRoom().transform, "fast");
			} else {
				cameraScript.MoveTo (GetCurrentRoom().transform, "slow");
			}

			// The foreman is busy, and cannot accept new commands.
			DeactivateForeman ();

		}
	}

	public void SnapCamera(int i) {
		Transform t = mainCamera.transform;
		Vector3 newPosition = new Vector3 (rooms[i].transform.position.x, rooms[i].transform.position.y, t.position.z);
		mainCamera.transform.position = newPosition;
	}

	private void SearchMap(Vector2 position) {
	
		// Don't allow further clicks when Foreman isn't ready.
		if (!ForemanReady()) {
			return;
		}

		containerColliders = Physics2D.OverlapCircleAll(position, searchRadius, containerActiveLayer);
		numContainerColliders = containerColliders.Length;
		numSectorColliders = Physics2D.OverlapCircleNonAlloc(position, searchRadius, sectorColliders, 1 << LayerMask.NameToLayer ("Sectors"));

		// Update the status of the container being clicked.
		if (numContainerColliders > 0) {
			DeactivateForeman();
			for (int i = 0; i < numContainerColliders; i++) {
				containerColliders[i].GetComponent<ContainerController> ().MakeFound();
			}
		}

		// Make sectors flicker when tapped.
		for (int k = 0; k < numSectorColliders; k++) {
			sectorColliders[k].GetComponent<FadeInOut> ().Flicker (0.8f);
		}
	}

	public void UpdateMap() {

		// Fire an event to let everyone know we're transitioning to a new room.
		if (OnAfterUpdate != null) {
			OnAfterUpdate ();
		}
	}

	private GameObject GetCurrentRoom() {
		return room;
	}

	public void SetRoom(int index) {
		currentRoomIndex = index;
		room = rooms [index];
		roomScript = room.GetComponent<RoomController> ();
		roomScript.selected = true;
		roomScript.OnReady += ActivateForeman;
		//Debug.Log ("SetRoom(" + index + ")");
	}

	private void Subscribe() {
		UserInputHandler.OnDirectionalKeyPressed += MoveMap;
		UserInputHandler.OnMouseClicked += SearchMap;
		PositionCamera.OnCameraReady += UpdateMap;
	}
	
	private void Unsubscribe() {
		UserInputHandler.OnDirectionalKeyPressed -= MoveMap;
		UserInputHandler.OnMouseClicked -= SearchMap;
		PositionCamera.OnCameraReady -= UpdateMap;
	}
}
