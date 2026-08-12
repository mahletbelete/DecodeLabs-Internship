gsap.registerPlugin(ScrollTrigger);

/* LOADING INTRO */
const loaderTl = gsap.timeline({
  defaults: { ease: "power3.inOut" }
});

loaderTl
//   .to(".loader-image", {
//     scale: 0.8,
//     opacity: 1,
//     duration: 0.7,
//     ease: "power2.out"
//   })
  .to(".loader-image", {
   scale: 2.5,
    opacity: 0,
    rotation: 0,
    duration: 1,
    ease: "power4.in"
  })
  .to(".loader", {
    opacity: 0,
    duration: 0.8,
    pointerEvents: "none"
  }, "-=0.25")
  .to(".overlay", {
    opacity: 0,
    duration: 0.7
  }, "-=0.4");

// ======================
// Initial States
// ======================
gsap.set([".title", ".subtitle", ".description", ".buttons", ".bean"], {
  opacity: 0,
  visibility: "hidden"
});

gsap.set(".title", {
  x: -120
});

gsap.set(".subtitle", {
  x: -100
});

gsap.set(".description", {
  y: 40
});

gsap.set(".buttons", {
  y: 40
});

gsap.set(".hero-image", {
  opacity: 0,
  scale: 2.6,
  x: -360,
  y: -90,
  rotation: -12,
  transformOrigin: "center center"
});

// ======================
// Main Timeline
// ======================
const heroTl = gsap.timeline({
  defaults: {
    ease: "power3.out"
  },
  delay: 0.25
});

heroTl
  .to(".hero-image", {
    opacity: 1,
    scale: 1,
    x: 0,
    y: 0,
    rotation: 0,
    duration: 1.6
  }, "-=0.25")
  .to(".title", {
    opacity: 1,
    visibility: "visible",
    x: 0,
    duration: 0.8
  }, "-=1.1")
  .to(".subtitle", {
    opacity: 1,
    visibility: "visible",
    x: 0,
    duration: 0.7
  }, "-=0.6")
  .to(".description", {
    opacity: 1,
    visibility: "visible",
    y: 0,
    duration: 0.7
  }, "-=0.45")
  .to(".buttons", {
    opacity: 1,
    visibility: "visible",
    y: 0,
    duration: 0.6
  }, "-=0.35")
  .to(".bean", {
    opacity: 1,
    visibility: "visible",
    scale: 1,
    duration: 0.7,
    stagger: 0.1
  }, "-=0.6");

// ======================
// Floating Coffee
// ======================
gsap.to(".hero-image", {
  y: "-=16",
  repeat: -1,
  yoyo: true,
  duration: 2.4,
  ease: "sine.inOut"
});

gsap.to(".bean1", {
  y: -18,
  rotation: 20,
  repeat: -1,
  yoyo: true,
  duration: 2
});

gsap.to(".bean2", {
  y: 18,
  rotation: -25,
  repeat: -1,
  yoyo: true,
  duration: 2.5
});

gsap.to(".bean3", {
  y: -12,
  rotation: 18,
  repeat: -1,
  yoyo: true,
  duration: 1.8
});

gsap.to(".bean4", {
  y: 15,
  rotation: -15,
  repeat: -1,
  yoyo: true,
  duration: 2.2
});
