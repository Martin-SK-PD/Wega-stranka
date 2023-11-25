import * as THREE from 'https://cdn.skypack.dev/three@0.129.0/build/three.module.js';
import { OrbitControls } from "https://unpkg.com/three@0.112/examples/jsm/controls/OrbitControls.js";
import { GLTFLoader } from 'https://cdn.skypack.dev/three@0.129.0/examples/jsm/loaders/GLTFLoader.js';

function init(containerId, modelPath) {
const container = document.getElementById(containerId);
const renderer = new THREE.WebGLRenderer({ antialias: true });
renderer.setSize(container.clientWidth, container.clientHeight);
container.appendChild(renderer.domElement);

const camera = new THREE.PerspectiveCamera(40, container.clientWidth / container.clientHeight, 1, 5000);
camera.rotation.y = 60 / 180 * Math.PI;
camera.position.x = 35;
camera.position.y = 0;
camera.position.z = 35;

const controls = new OrbitControls(camera, renderer.domElement);

const scene = new THREE.Scene();
scene.background = new THREE.Color(0xdddddd);

const hlight = new THREE.AmbientLight(0xf1ffd9, 0.6);
scene.add(hlight);

const directionalLight = new THREE.DirectionalLight(0xffffff, 0.6);
directionalLight.position.set(0, 1, 0);
directionalLight.castShadow = true;
scene.add(directionalLight);


const loader = new GLTFLoader();
loader.load(modelPath, function (gltf) {
    const car = gltf.scene.children[0];
    car.scale.set(0.5, 0.5, 0.5);
    scene.add(gltf.scene);
    animate();
});

function animate() {
renderer.render(scene, camera);
requestAnimationFrame(animate);
}
}

init('model', '3D models/f1.gltf');