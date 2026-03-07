import {
  AmbientLight,
  BoxGeometry,
  Clock,
  ConeGeometry,
  EdgesGeometry,
  Fog,
  Group,
  LineBasicMaterial,
  LineSegments,
  Mesh,
  MeshStandardMaterial,
  PerspectiveCamera,
  PointLight,
  Scene,
  SRGBColorSpace,
  TorusGeometry,
  Vector3,
  WebGLRenderer,
  CylinderGeometry,
} from "https://cdn.jsdelivr.net/npm/three@0.159.0/build/three.module.js";

const stage = document.getElementById("three-stage");

const scene = new Scene();
scene.fog = new Fog(0xf2e6d8, 6, 18);

const camera = new PerspectiveCamera(45, 1, 0.1, 100);
camera.position.set(0, 1.2, 7);

const renderer = new WebGLRenderer({ antialias: true, alpha: true });
renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
renderer.setSize(stage.clientWidth, stage.clientHeight);
renderer.outputColorSpace = SRGBColorSpace;
stage.appendChild(renderer.domElement);

const ambient = new AmbientLight(0xffffff, 0.8);
scene.add(ambient);

const keyLight = new PointLight(0xc48a4a, 1.2, 30);
keyLight.position.set(4, 5, 6);
scene.add(keyLight);

const rimLight = new PointLight(0xa0703e, 0.6, 20);
rimLight.position.set(-4, -2, -4);
scene.add(rimLight);

const boxGeo = new BoxGeometry(2, 2, 2);
const boxMat = new MeshStandardMaterial({
  color: 0x8b6b4a,
  metalness: 0.1,
  roughness: 0.55,
  emissive: 0x2b1f14,
  transparent: true,
  opacity: 0.88,
});
const box = new Mesh(boxGeo, boxMat);
scene.add(box);

const edge = new LineSegments(
  new EdgesGeometry(boxGeo),
  new LineBasicMaterial({
    color: 0xc6a47a,
    transparent: true,
    opacity: 0.4,
  }),
);
scene.add(edge);

const items = [];
const palette = [0xc48a4a, 0xa7754a, 0x6f5a3e, 0xd2b48c, 0x8c5d3e];

function mat(color) {
  return new MeshStandardMaterial({
    color,
    metalness: 0.2,
    roughness: 0.45,
  });
}

function buildPhone() {
  const group = new Group();
  const body = new Mesh(new BoxGeometry(0.34, 0.62, 0.06), mat(0x2a2622));
  const screen = new Mesh(new BoxGeometry(0.28, 0.5, 0.01), mat(0x1c1b1a));
  screen.position.z = 0.035;
  group.add(body, screen);
  return group;
}

function buildKeys() {
  const group = new Group();
  const ring = new Mesh(new TorusGeometry(0.16, 0.04, 10, 16), mat(0xd2b48c));
  ring.rotation.x = Math.PI / 2;
  const keyBase = new Mesh(new BoxGeometry(0.08, 0.24, 0.03), mat(0xa7754a));
  keyBase.position.set(0.18, -0.05, 0);
  const keyTeeth = new Mesh(new BoxGeometry(0.06, 0.08, 0.03), mat(0xa7754a));
  keyTeeth.position.set(0.18, -0.17, 0);
  group.add(ring, keyBase, keyTeeth);
  return group;
}

function buildWallet() {
  const group = new Group();
  const shell = new Mesh(new BoxGeometry(0.4, 0.26, 0.08), mat(0x6f5a3e));
  const flap = new Mesh(new BoxGeometry(0.38, 0.12, 0.02), mat(0x8c5d3e));
  flap.position.set(0, 0.07, 0.05);
  group.add(shell, flap);
  return group;
}

function buildBackpack() {
  const group = new Group();
  const body = new Mesh(new BoxGeometry(0.42, 0.5, 0.22), mat(0xc48a4a));
  const pocket = new Mesh(new BoxGeometry(0.28, 0.18, 0.14), mat(0x8a6b48));
  pocket.position.set(0, -0.14, 0.18);
  const handle = new Mesh(new TorusGeometry(0.12, 0.03, 8, 12), mat(0x6f5a3e));
  handle.rotation.x = Math.PI / 2;
  handle.position.set(0, 0.26, 0.05);
  group.add(body, pocket, handle);
  return group;
}

function buildGlasses() {
  const group = new Group();
  const left = new Mesh(new TorusGeometry(0.16, 0.035, 8, 14), mat(0x2a2622));
  const right = left.clone();
  left.position.x = -0.2;
  right.position.x = 0.2;
  const bridge = new Mesh(new BoxGeometry(0.12, 0.04, 0.03), mat(0x2a2622));
  group.add(left, right, bridge);
  return group;
}

function buildHeadphones() {
  const group = new Group();
  const band = new Mesh(new TorusGeometry(0.3, 0.05, 10, 18), mat(0x6f5a3e));
  band.rotation.x = Math.PI / 2;
  const earLeft = new Mesh(new BoxGeometry(0.12, 0.16, 0.08), mat(0xa7754a));
  const earRight = earLeft.clone();
  earLeft.position.set(-0.3, -0.1, 0);
  earRight.position.set(0.3, -0.1, 0);
  group.add(band, earLeft, earRight);
  return group;
}

function buildBottle() {
  const group = new Group();
  const body = new Mesh(
    new CylinderGeometry(0.16, 0.18, 0.5, 10),
    mat(0xd2b48c),
  );
  const cap = new Mesh(new ConeGeometry(0.12, 0.14, 8), mat(0x8a6b48));
  cap.position.y = 0.32;
  group.add(body, cap);
  return group;
}

const builders = [
  buildPhone,
  buildKeys,
  buildWallet,
  buildBackpack,
  buildGlasses,
  buildHeadphones,
  buildBottle,
];

function spawnItem() {
  const mesh = builders[Math.floor(Math.random() * builders.length)]();
  mesh.traverse((child) => {
    if (child.isMesh) {
      child.material = mat(palette[Math.floor(Math.random() * palette.length)]);
    }
  });

  mesh.position.set(0, 0, 0);
  mesh.velocity = new Vector3(
    (Math.random() - 0.5) * 0.08,
    0.06 + Math.random() * 0.08,
    (Math.random() - 0.5) * 0.08,
  );
  mesh.spin = new Vector3(
    (Math.random() - 0.5) * 0.06,
    (Math.random() - 0.5) * 0.06,
    (Math.random() - 0.5) * 0.06,
  );

  scene.add(mesh);
  items.push(mesh);
}

const spawnInterval = setInterval(spawnItem, 420);

function cleanupItems() {
  for (let i = items.length - 1; i >= 0; i -= 1) {
    const item = items[i];
    if (item.position.length() > 10) {
      scene.remove(item);
      item.traverse((child) => {
        if (child.isMesh) {
          child.geometry.dispose();
          child.material.dispose();
        }
      });
      items.splice(i, 1);
    }
  }
}

const clock = new Clock();

function animate() {
  const t = clock.getElapsedTime();
  box.rotation.y = t * 0.35;
  edge.rotation.copy(box.rotation);

  items.forEach((item) => {
    item.position.add(item.velocity);
    item.rotation.x += item.spin.x;
    item.rotation.y += item.spin.y;
    item.rotation.z += item.spin.z;
  });

  cleanupItems();
  renderer.render(scene, camera);
  requestAnimationFrame(animate);
}

animate();

function resizeRenderer() {
  const { clientWidth, clientHeight } = stage;
  camera.aspect = clientWidth / clientHeight;
  camera.updateProjectionMatrix();
  renderer.setSize(clientWidth, clientHeight);
}

window.addEventListener("resize", resizeRenderer);
resizeRenderer();

window.addEventListener("beforeunload", () => {
  clearInterval(spawnInterval);
  renderer.dispose();
});
