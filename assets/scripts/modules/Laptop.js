import Device from './Device';

export default class extends Device {
    constructor(m) {
        super(m);

        this.settings = {
            camera: {
                fov: 25,
                position: [0,0,0]
            }
        }

        this.file = 'laptop_v2.glb'

    }

    initGui() {
        super.initGui()

        let guiController = function() {
            this.rotation = { x: 0, y: 0, z: 0 }
            this.color = '#ffffff'
        };

        let guiInstance = new guiController();

        // Device
        let device = this.gui.addFolder('device')
        // device.add(this.deviceMaterial, 'roughness', 0 , 1)
        // device.add(this.deviceMaterial, 'clearCoat', 0 , 1)
        // device.add(this.deviceMaterial, 'clearCoatRoughness', 0 , 1)

        let deviceRotation = device.addFolder('Rotation')
        let rotationXController = deviceRotation.add(guiInstance.rotation, 'x', -180, 180);
        let rotationYController = deviceRotation.add(guiInstance.rotation, 'y', -180, 180);
        let rotationZController = deviceRotation.add(guiInstance.rotation, 'z', -180, 180);

        rotationXController.onChange((value) => {
            this.object.rotation.x = value * Math.PI / 180;
        });

        rotationYController.onChange((value) => {
            this.object.rotation.y = value * Math.PI / 180;
        });

        rotationZController.onChange((value) => {
            this.object.rotation.z = value * Math.PI / 180;
        });


        let devicePosition = device.addFolder('Position')
        devicePosition.add(this.object.position, 'x', -10, 10);
        devicePosition.add(this.object.position, 'y', -10, 10);
        devicePosition.add(this.object.position, 'z', -10, 10);
        device.open();


        // LIGHT
        // ==========================================================================

        let lightPosition = device.addFolder('Light position')
        lightPosition.add(this.spotLight.position, 'x', -10, 10);
        lightPosition.add(this.spotLight.position, 'y', -10, 10);
        lightPosition.add(this.spotLight.position, 'z', -10, 10);
    }

    setupObject() {

        this.deviceMaterial = new THREE.MeshLambertMaterial({
            color: new THREE.Color(0xffffff),
            map: this.texture
        })

        this.reposition()

        this.object.rotation.x = 38 * Math.PI / 180;
        this.object.rotation.y = -26 * Math.PI / 180;
        this.object.rotation.z = 6 * Math.PI / 180;

        this.object.children[0].material = this.deviceMaterial
    }

    reposition() {
        if(this.object && this.object.position) {
            if(window.innerWidth > 700) {
                this.object.position.x = 0.15
                this.object.position.y = 0.1
                this.object.position.z = 0

                this.camera.position.z = 12.5
            } else {
                this.object.position.x = -0.1
                this.object.position.y = 0.1
                this.object.position.z = 0

                this.camera.position.z = 10
            }
        }
    }

    toggle(e) {
        super.toggle(e)
    }

    resize() {
        super.resize()

        this.reposition();
    }

    destroy() {
        super.destroy();
        this.$el.off(`.${EVENT_NAMESPACE}`);
    }
}
