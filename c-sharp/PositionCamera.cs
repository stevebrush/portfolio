using UnityEngine;
using System.Collections;

public class PositionCamera : MonoBehaviour {

	//private float[] easings = {15f, 15f, 15f};
	private float[] easings = {5f, 10f, 15f};

	private Transform _t;
	private float z;

	public delegate void DoneMoving();
	public static event DoneMoving OnCameraReady;
	
	void Start () {
		z = transform.position.z;
	}

	void Update () {
	
	}

	public void SnapTo(Transform target) {
		transform.position = new Vector3 (target.transform.position.x, target.transform.position.y, transform.position.z);
	}

	public void MoveTo(Transform target, string speed = "slow") {
		_t = target;
		StartCoroutine (Focus(GetEasing(speed)));
	}

	private float GetEasing(string speed) {
		float easing;
		switch (speed) {
		case "slow":
			easing = easings[0];
			break;
		case "medium":
		default:
			easing = easings[1];
			break;
		case "fast":
			easing = easings[2];
			break;
		}
		return easing;
	}

	IEnumerator Focus(float easing) {
		while (Vector2.Distance(transform.position, _t.position) > 0.01f) {
			transform.position = Vector3.Lerp(transform.position, new Vector3(_t.position.x, _t.position.y, z), easing * Time.deltaTime);
			yield return null; // This is a failsafe, yeilding the while loop to the program on each update
		}
		if (OnCameraReady != null) {
			OnCameraReady ();
		}
	}
}
