/**
 * BookManager Animation & UI System
 * Implements GSAP Timeline, ScrollTrigger, Plugins, Performance and Lucide Icons
 * Guided by /gsap-timeline, /gsap-scrolltrigger, /gsap-performance, /icon-design, and /ui-ux-pro-max
 */

(function () {
    'use strict';

    // 1. Initialiser Lucide Icons
    function initIcons() {
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons({
                attrs: {
                    'stroke-width': 1.75
                }
            });
        }
    }

    // Ré-exécuter lors de mutations DOM ou événements Alpine
    window.initLucideIcons = initIcons;
    document.addEventListener('alpine:init', () => {
        initIcons();
    });
    document.addEventListener('alpine:navigated', () => {
        initIcons();
    });

    // 2. Initialiser les animations GSAP
    function initGsapAnimations() {
        if (!window.gsap) return;

        // Enregistrer les plugins GSAP (gsap-plugins)
        if (window.ScrollTrigger) {
            gsap.registerPlugin(ScrollTrigger);
        }
        if (window.ScrollToPlugin) {
            gsap.registerPlugin(ScrollToPlugin);
        }

        // Support complet de l'accessibilité : prefers-reduced-motion
        const mm = gsap.matchMedia();

        mm.add("(prefers-reduced-motion: reduce)", () => {
            // Mode sans mouvement : affichage direct sans décalage
            gsap.set(".gsap-fade-in, .gsap-card, .gsap-batch-item, tbody tr", {
                opacity: 1,
                x: 0,
                y: 0,
                scale: 1,
                clearProps: "all"
            });
            // Compteurs immédiats
            document.querySelectorAll('.stat-counter').forEach(el => {
                const target = parseInt(el.getAttribute('data-target') || '0', 10);
                el.textContent = isNaN(target) ? '0' : target.toLocaleString('fr-FR');
            });
        });

        mm.add("(prefers-reduced-motion: no-preference)", () => {
            // Master Timeline pour le chargement de la page (gsap-timeline)
            const masterTl = gsap.timeline({
                defaults: {
                    ease: "power2.out",
                    duration: 0.45
                }
            });

            // Flashes messages
            const flashes = document.querySelectorAll('#flash-success, #flash-warning, #flash-error');
            if (flashes.length > 0) {
                masterTl.from(flashes, {
                    y: -16,
                    opacity: 0,
                    duration: 0.4,
                    stagger: 0.08,
                    ease: "power3.out",
                    clearProps: "transform,opacity"
                }, 0);
            }

            // Entrée douce des sections principales
            const mainHeader = document.querySelector('main > div > div:first-child');
            if (mainHeader) {
                masterTl.from(mainHeader, {
                    y: 12,
                    opacity: 0,
                    duration: 0.4,
                    clearProps: "transform,opacity"
                }, flashes.length > 0 ? "<0.1" : 0);
            }

            // Batch Reveal pour les cartes de statistiques (gsap-scrolltrigger + gsap-performance)
            if (window.ScrollTrigger) {
                ScrollTrigger.batch(".gsap-card", {
                    interval: 0.06,
                    batchMax: 4,
                    onEnter: (batch) => {
                        gsap.fromTo(batch, 
                            { opacity: 0, y: 16 },
                            {
                                opacity: 1,
                                y: 0,
                                duration: 0.5,
                                stagger: 0.08,
                                ease: "power2.out",
                                overwrite: "auto",
                                clearProps: "transform,opacity"
                            }
                        );
                    },
                    once: true
                });

                // Batch Reveal pour les éléments répétés / listes
                ScrollTrigger.batch(".gsap-batch-item", {
                    interval: 0.05,
                    batchMax: 6,
                    onEnter: (batch) => {
                        gsap.fromTo(batch,
                            { opacity: 0, y: 14 },
                            {
                                opacity: 1,
                                y: 0,
                                duration: 0.4,
                                stagger: 0.05,
                                ease: "power2.out",
                                overwrite: "auto",
                                clearProps: "transform,opacity"
                            }
                        );
                    },
                    once: true
                });

                // Lignes de tableau avec ScrollTrigger.batch (performant et fluide)
                ScrollTrigger.batch("tbody tr", {
                    interval: 0.04,
                    batchMax: 8,
                    onEnter: (batch) => {
                        gsap.fromTo(batch,
                            { opacity: 0, x: -8 },
                            {
                                opacity: 1,
                                x: 0,
                                duration: 0.35,
                                stagger: 0.03,
                                ease: "power1.out",
                                overwrite: "auto",
                                clearProps: "transform,opacity"
                            }
                        );
                    },
                    once: true
                });
            }

            // Compteurs de statistiques avec ScrollTrigger (s'active quand l'utilisateur le voit)
            document.querySelectorAll('.stat-counter').forEach(el => {
                const target = parseInt(el.getAttribute('data-target') || '0', 10);
                if (isNaN(target)) return;

                const counterObj = { val: 0 };
                const tween = gsap.to(counterObj, {
                    val: target,
                    duration: 1.2,
                    ease: "power2.out",
                    paused: true,
                    onUpdate: () => {
                        el.textContent = Math.floor(counterObj.val).toLocaleString('fr-FR');
                    }
                });

                if (window.ScrollTrigger) {
                    ScrollTrigger.create({
                        trigger: el,
                        start: "top 95%",
                        onEnter: () => tween.play(),
                        once: true
                    });
                } else {
                    tween.play();
                }
            });

            // Animation des barres d'histogramme (gsap-performance : GPU scaleY)
            const chartBars = document.querySelectorAll('.chart-bar');
            if (chartBars.length > 0) {
                gsap.fromTo(chartBars, 
                    { scaleY: 0, transformOrigin: "bottom" },
                    {
                        scaleY: 1,
                        duration: 0.8,
                        stagger: 0.07,
                        ease: "back.out(1.3)",
                        scrollTrigger: window.ScrollTrigger ? {
                            trigger: chartBars[0].parentElement,
                            start: "top 90%",
                            once: true
                        } : undefined,
                        clearProps: "transform"
                    }
                );
            }

            // Animation du graphique circulaire Donut (gsap-scrolltrigger)
            const donut = document.getElementById('repartition-donut');
            if (donut) {
                gsap.fromTo(donut,
                    { scale: 0.85, opacity: 0, rotation: -30 },
                    {
                        scale: 1,
                        opacity: 1,
                        rotation: 0,
                        duration: 0.7,
                        ease: "power3.out",
                        scrollTrigger: window.ScrollTrigger ? {
                            trigger: donut,
                            start: "top 90%",
                            once: true
                        } : undefined,
                        clearProps: "opacity,scale"
                    }
                );
            }

            // Faisceau laser scanner continu (gsap-timeline avec yoyo)
            const laser = document.getElementById('scanner-laser');
            if (laser) {
                gsap.fromTo(laser,
                    { y: 0 },
                    {
                        y: 104,
                        duration: 1.5,
                        repeat: -1,
                        yoyo: true,
                        ease: "sine.inOut"
                    }
                );
            }

            // Bouton Scroll to top
            const scrollTopBtn = document.getElementById('scroll-to-top');
            if (scrollTopBtn && window.ScrollTrigger) {
                ScrollTrigger.create({
                    start: "top -200",
                    onUpdate: (self) => {
                        if (self.direction === 1 && window.scrollY > 300) {
                            gsap.to(scrollTopBtn, { opacity: 1, y: 0, duration: 0.2, pointerEvents: "auto" });
                        } else if (window.scrollY <= 300) {
                            gsap.to(scrollTopBtn, { opacity: 0, y: 10, duration: 0.2, pointerEvents: "none" });
                        }
                    }
                });

                scrollTopBtn.addEventListener('click', () => {
                    if (window.ScrollToPlugin) {
                        gsap.to(window, { duration: 0.6, scrollTo: 0, ease: "power2.inOut" });
                    } else {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                });
            }
        });
    }

    // Initialisation globale au chargement
    document.addEventListener('DOMContentLoaded', () => {
        initIcons();
        initGsapAnimations();
    });

    // Exposer l'utilitaire d'animation pour les interactions dynamiques
    window.BookManagerAnimations = {
        refreshIcons: initIcons,
        animateCoverPreview: (selector) => {
            if (window.gsap) {
                gsap.from(selector, { scale: 0.92, opacity: 0, duration: 0.4, ease: "back.out(1.5)" });
            }
        },
        pulseElement: (selector) => {
            if (window.gsap) {
                gsap.fromTo(selector, 
                    { scale: 0.98 }, 
                    { scale: 1, duration: 0.3, ease: "elastic.out(1.2, 0.4)" }
                );
            }
        }
    };
})();
