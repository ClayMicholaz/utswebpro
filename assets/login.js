import {
Scene,
PerspectiveCamera,
WebGLRenderer,
AmbientLight,
PointLight,
Mesh,
BoxGeometry,
MeshStandardMaterial,
Group,
Vector3,
Clock,
CylinderGeometry,
TorusGeometry,
ConeGeometry
} from "https://cdn.jsdelivr.net/npm/three@0.159.0/build/three.module.js";

const stage = document.getElementById("three-stage");

const scene = new Scene();

const camera = new PerspectiveCamera(45, window.innerWidth/window.innerHeight,0.1,100);
camera.position.set(0,1.2,7);

const renderer = new WebGLRenderer({antialias:true,alpha:true});
renderer.setSize(window.innerWidth,window.innerHeight);
stage.appendChild(renderer.domElement);

const ambient = new AmbientLight(0xffffff,0.8);
scene.add(ambient);

const light = new PointLight(0xc48a4a,1.2,30);
light.position.set(4,5,6);
scene.add(light);

/* BOX CENTER */

const box = new Mesh(
new BoxGeometry(2,2,2),
new MeshStandardMaterial({
color:0x8b6b4a,
roughness:0.6
})
);

scene.add(box);

/* ITEMS */

const items=[];

function createPhone(){
const g=new Group();

const body=new Mesh(
new BoxGeometry(0.34,0.62,0.06),
new MeshStandardMaterial({color:0x2a2622})
);

g.add(body);
return g;
}

function createKeys(){
const g=new Group();

const ring=new Mesh(
new TorusGeometry(0.16,0.04,10,16),
new MeshStandardMaterial({color:0xd2b48c})
);

ring.rotation.x=Math.PI/2;

g.add(ring);
return g;
}

function createBottle(){
const g=new Group();

const body=new Mesh(
new CylinderGeometry(0.16,0.18,0.5,10),
new MeshStandardMaterial({color:0xd2b48c})
);

const cap=new Mesh(
new ConeGeometry(0.12,0.14,8),
new MeshStandardMaterial({color:0x8a6b48})
);

cap.position.y=0.32;

g.add(body,cap);
return g;
}

const builders=[createPhone,createKeys,createBottle];

function spawnItem(){

const mesh=builders[Math.floor(Math.random()*builders.length)]();

mesh.position.set(0,0,0);

mesh.velocity=new Vector3(
(Math.random()-0.5)*0.08,
0.06+Math.random()*0.08,
(Math.random()-0.5)*0.08
);

mesh.spin=new Vector3(
(Math.random()-0.5)*0.06,
(Math.random()-0.5)*0.06,
(Math.random()-0.5)*0.06
);

scene.add(mesh);
items.push(mesh);

}

setInterval(spawnItem,600);

const clock=new Clock();

function animate(){

const t=clock.getElapsedTime();

box.rotation.y=t*0.4;

items.forEach(item=>{
item.position.add(item.velocity);
item.rotation.x+=item.spin.x;
item.rotation.y+=item.spin.y;
item.rotation.z+=item.spin.z;
});

renderer.render(scene,camera);

requestAnimationFrame(animate);

}

animate();

/* RESIZE */

window.addEventListener("resize",()=>{

camera.aspect=window.innerWidth/window.innerHeight;
camera.updateProjectionMatrix();

renderer.setSize(window.innerWidth,window.innerHeight);

});