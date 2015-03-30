using UnityEngine;
using System.Collections;
using System.Collections.Generic;

public class RoomController : MonoBehaviour {

	// Events.
	public delegate void RoomEvent();
	public event RoomEvent OnEnter; // Room has faded into view
	public event RoomEvent OnReady; // Room has loaded all of its containers and enemies
	public event RoomEvent OnExit; // Before room is faded out

	public int placementIndex;
	public int storageIndex;
	public int[] doorIndex = {-1, -1, -1, -1};
	public int numDoors = 0;

	public bool locked = false;
	public bool visited = false;
	public bool selected = false;

	public bool containersReady = false;
	public bool roomReady = false;

	public Sprite[] sprites;
	public string resourceName;

	private List<GameObject> containers;
	private int numContainers = 0;
	private FadeInOut fadeInOut;
	private ContainerController containerController;
	private int numContainersReady = 0;

	void Start() {
		//Debug.Log ("RoomController Start();");
		fadeInOut = gameObject.GetComponent<FadeInOut> ();
		fadeInOut.Hide ();
		Subscribe ();
		CheckEnter ();
	}
	
	void OnDisable() {
		Unsubscribe ();
	}
	
	public void SetContainers(List<GameObject> cont) {

		containers = cont;
		numContainers = containers.Count;

		// If there are containers in this room, subscribe to the first one's ready event.
		if (numContainers > 0) {
			//Debug.Log ("There are many containers!");
			for (int i = 0; i < numContainers; i++) {
				containerController = containers [i].GetComponent<ContainerController> ();
				containerController.OnContainerReady += CheckContainersReady;
			}
		}
		
		// No containers exist, so no need to check their ready states.
		else {
			//Debug.Log ("There are no containers!");
			containersReady = true;
		}
	}

	private void CheckEnter() {

		// Only check enter if the room is the current room.
		if (!selected) {
			return;
		}

		//Debug.Log ("RoomController: CheckEnter()...");

		if (visited) {
			fadeInOut.FadeIn ("fast");
		} else {
			fadeInOut.FadeIn ("slow");
		}

		if (OnEnter != null) {
			OnEnter ();
		} else {
			//Debug.Log ("RoomController: Nothing assigned...");	
		}
	}

	private void CheckExit() {

		//Debug.Log ("CheckExit(): Checking... " + selected);

		// Only check enter if the room is the current room.
		if (!selected) {
			//Debug.Log ("CheckExit(): We're not in the current room, cancelling.");
			return;
		}

		if (visited) {
			fadeInOut.FadeAuto (0.2f, "fast");
		} else {
			fadeInOut.FadeAuto (0.2f, "slow");
		}

		if (OnExit != null) {
			OnExit();
		}
	}

	private void CheckContainersReady() {
		//numContainersReady++;
		//if (numContainersReady >= numContainers) {
			containersReady = true;
			CheckReady ();
		//}
	}

	private void CheckRoomReady() {
		roomReady = true;
		CheckReady ();
	}

	private void CheckReady() {

		if (!selected) {
			return;
		}

		//Debug.Log ("Room CheckReady(): " + roomReady + " || " + containersReady);

		// Both the room and the containers need to be ready!
		if (!selected || !roomReady || !containersReady) {
			return;	
		}

		roomReady = false;
		if (numContainers > 0) {
			containersReady = false;
			//numContainersReady = 0;
		}

		if (OnReady != null && selected == true) {
			//Debug.Log ("Checking ready...");
			OnReady ();
		} else {
			//Debug.Log ("There are no subscriptions for OnRoomReady();");	
		}
	}

	public void SetSpriteMap() {
		
		float rotation = 0.0f;

		bool upIsDoor = false;
		bool rightIsDoor = false;
		bool downIsDoor = false;
		bool leftIsDoor = false;

		if (doorIndex[Config.UP] > -1) {
			upIsDoor = true;
		}
		if (doorIndex[Config.RIGHT] > -1) {
			rightIsDoor = true;
		}
		if (doorIndex[Config.DOWN] > -1) {
			downIsDoor = true;
		}
		if (doorIndex[Config.LEFT] > -1) {
			leftIsDoor = true;
		}
		
		switch (numDoors) {
			
		case 4:
			// 4-way
			resourceName = "rooms-4";
			break;
			
		case 3:

			resourceName = "rooms-3";

			if (!upIsDoor) {
				rotation = 0.0f;
			} else if (!rightIsDoor) {
				rotation = -90.0f;
			} else if (!downIsDoor) {
				rotation = 180.0f;
			} else if (!leftIsDoor) {
				rotation = -270.0f;
			}
			break;
			
		case 2:
			if (upIsDoor) {
				
				if (!rightIsDoor && !leftIsDoor) {

					// It's vertical, don't affect rotation (N,S)
					resourceName = "rooms-2-hall";
					rotation = 0.0f;
				} 
				
				// It's an L-shape
				else {

					resourceName = "rooms-2-l";

					if (rightIsDoor) {
						// no rotation (N,E)
						rotation = 0.0f;
					} else {
						// rotation (N,W)
						rotation = -270.0f;
					}
				}
				
			} else {
				
				if (downIsDoor) {

					resourceName = "rooms-2-l";

					// It's an L-shape
					if (rightIsDoor) {
						// L-shape, rotation (E,S)
						rotation = -90.0f;
					} else {
						// L-shape, rotation (W,S)
						rotation = 180.0f;
					}
					
				} else {

					// It's horizontal, rotation should be 90 degrees (E,W)
					resourceName = "rooms-2-hall";
					rotation = -90.0f;

				}
			}
			break;
			
		case 1:
			resourceName = "rooms-1";
			if (upIsDoor) {
				rotation = 0.0f;
			} else if (rightIsDoor) {
				rotation = -90.0f;
			} else if (downIsDoor) {
				rotation = 180.0f;
			} else if (leftIsDoor) {
				rotation = -270.0f;
			}
			break;
		}

		sprites = Resources.LoadAll<Sprite> (resourceName);
		GetComponent<SpriteRenderer>().sprite = sprites[Random.Range (0, sprites.Length)];

		transform.Rotate(new Vector3(0.0f, 0.0f, rotation));

	}

	private void Subscribe() {
		Foreman.OnBeforeUpdate += CheckExit;
		Foreman.OnAfterUpdate += CheckEnter;
		fadeInOut.OnFadeInComplete += CheckRoomReady;
	}

	private void Unsubscribe() {
		if (containers != null && containers.Count > 0) {
			numContainers = containers.Count;
			for (int i = 0; i < numContainers; i++) {
				if (containers[i] == null) {
					continue;
				}
				containerController = containers [i].GetComponent<ContainerController> ();
				containerController.OnContainerReady -= CheckContainersReady;
			}
		}
		if (fadeInOut != null) {
			fadeInOut.OnFadeInComplete -= CheckRoomReady;
		}
		Foreman.OnBeforeUpdate -= CheckExit;
		Foreman.OnAfterUpdate -= CheckEnter;
	}
}
