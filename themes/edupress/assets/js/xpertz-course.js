/**
 * Accessible interactions for the XPERTZ LearnPress course page.
 */

(function () {
	'use strict';

	const root = document.querySelector('.xpc-course');
	if (!root) {
		return;
	}

	const config = window.xpertzCourse || {};
	const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/**
	 * Read local storage without breaking restricted browser contexts.
	 *
	 * @param {string} key Storage key.
	 * @return {string|null} Stored value.
	 */
	function getStoredValue(key) {
		try {
			return window.localStorage.getItem(key);
		} catch (error) {
			return null;
		}
	}

	/**
	 * Persist a local preference when storage is available.
	 *
	 * @param {string} key Storage key.
	 * @param {string} value Storage value.
	 */
	function setStoredValue(key, value) {
		try {
			window.localStorage.setItem(key, value);
		} catch (error) {
			// The interaction remains usable when storage is unavailable.
		}
	}

	// Sticky section navigation and active section state.
	const nav = root.querySelector('.xpc-course-nav');
	const navLinks = nav ? Array.from(nav.querySelectorAll('a[href^="#"]')) : [];
	const sections = navLinks
		.map((link) => document.querySelector(link.getAttribute('href')))
		.filter(Boolean);

	navLinks.forEach((link) => {
		link.addEventListener('click', (event) => {
			const target = document.querySelector(link.getAttribute('href'));
			if (!target) {
				return;
			}

			event.preventDefault();
			target.scrollIntoView({ behavior: reducedMotion ? 'auto' : 'smooth', block: 'start' });
			window.history.replaceState(null, '', link.getAttribute('href'));
		});
	});

	if ('IntersectionObserver' in window && sections.length) {
		const sectionObserver = new IntersectionObserver(
			(entries) => {
				const visible = entries
					.filter((entry) => entry.isIntersecting)
					.sort((first, second) => second.intersectionRatio - first.intersectionRatio)[0];

				if (!visible) {
					return;
				}

				navLinks.forEach((link) => {
					const active = link.getAttribute('href') === `#${visible.target.id}`;
					link.classList.toggle('is-active', active);
					if (active) {
						link.setAttribute('aria-current', 'true');
					} else {
						link.removeAttribute('aria-current');
					}
				});
			},
			{ rootMargin: '-20% 0px -65% 0px', threshold: [0, 0.1, 0.3] }
		);

		sections.forEach((section) => sectionObserver.observe(section));
	}

	// Improve LearnPress curriculum headers without replacing its own behavior.
	root.querySelectorAll('.xpc-curriculum .course-section').forEach((section, index) => {
		const header = section.querySelector('.course-section-header');
		const panel = section.querySelector('.course-section__items');
		if (!header || !panel) {
			return;
		}

		if (!panel.id) {
			panel.id = `xpc-course-section-${index + 1}`;
		}

		header.setAttribute('role', 'button');
		header.setAttribute('tabindex', '0');
		header.setAttribute('aria-controls', panel.id);
		header.setAttribute('aria-expanded', section.classList.contains('lp-collapse') ? 'false' : 'true');

		const updateCurriculumState = () => {
			window.requestAnimationFrame(() => {
				header.setAttribute('aria-expanded', section.classList.contains('lp-collapse') ? 'false' : 'true');
			});
		};

		header.addEventListener('click', updateCurriculumState);
		header.addEventListener('keydown', (event) => {
			if (event.key === 'Enter' || event.key === ' ') {
				event.preventDefault();
				header.click();
			}
		});
	});

	// FAQ accordion and search.
	const faqItems = Array.from(root.querySelectorAll('[data-faq-item]'));
	faqItems.forEach((item) => {
		const button = item.querySelector('button[aria-controls]');
		const panel = button ? document.getElementById(button.getAttribute('aria-controls')) : null;
		if (!button || !panel) {
			return;
		}

		button.addEventListener('click', () => {
			const willOpen = button.getAttribute('aria-expanded') !== 'true';
			button.setAttribute('aria-expanded', String(willOpen));
			panel.hidden = !willOpen;
		});
	});

	const faqSearch = root.querySelector('[data-faq-search]');
	if (faqSearch) {
		const emptyMessage = root.querySelector('.xpc-faq-empty');
		faqSearch.addEventListener('input', () => {
			const query = faqSearch.value.trim().toLocaleLowerCase();
			let visibleCount = 0;

			faqItems.forEach((item) => {
				const matches = !query || item.textContent.toLocaleLowerCase().includes(query);
				item.classList.toggle('is-hidden', !matches);
				visibleCount += matches ? 1 : 0;
			});

			if (emptyMessage) {
				emptyMessage.hidden = visibleCount !== 0;
			}
		});
	}

	// Locally persisted course wishlist preference.
	root.querySelectorAll('[data-wishlist-course]').forEach((button) => {
		const key = `xpertz-wishlist-${button.dataset.wishlistCourse}`;
		const label = button.querySelector('span');
		const defaultLabel = label ? label.textContent : '';
		const updateWishlist = (saved) => {
			button.setAttribute('aria-pressed', String(saved));
			if (label) {
				label.textContent = saved ? config.savedLabel || 'Saved' : defaultLabel;
			}
		};

		updateWishlist(getStoredValue(key) === '1');
		button.addEventListener('click', () => {
			const saved = button.getAttribute('aria-pressed') !== 'true';
			setStoredValue(key, saved ? '1' : '0');
			updateWishlist(saved);
		});
	});

	// Native share sheet with a clipboard fallback.
	root.querySelectorAll('[data-share-course]').forEach((button) => {
		const label = button.querySelector('span');
		const defaultLabel = label ? label.textContent : '';
		button.addEventListener('click', async () => {
			const shareData = { title: button.dataset.title || document.title, url: button.dataset.url || window.location.href };

			try {
				if (navigator.share) {
					await navigator.share(shareData);
					return;
				}

				await navigator.clipboard.writeText(shareData.url);
				if (label) {
					label.textContent = config.copiedLabel || 'Link copied';
					window.setTimeout(() => {
						label.textContent = defaultLabel;
					}, 1800);
				}
			} catch (error) {
				// Cancelled share sheets and denied clipboard access need no alert.
			}
		});
	});

	// Helpful and report review actions, guarded against repeat clicks per browser.
	root.querySelectorAll('[data-review-action]').forEach((button) => {
		const mode = button.dataset.reviewAction;
		const commentId = button.dataset.commentId;
		const storageKey = `xpertz-review-${mode}-${commentId}`;

		if (getStoredValue(storageKey) === '1') {
			button.classList.add('is-active');
			button.disabled = true;
		}

		button.addEventListener('click', async () => {
			if (!config.ajaxUrl || !config.reviewNonce || button.disabled) {
				return;
			}

			button.disabled = true;
			const data = new window.URLSearchParams({
				action: 'xpertz_course_review_action',
				nonce: config.reviewNonce,
				commentId,
				mode,
			});

			try {
				const response = await window.fetch(config.ajaxUrl, {
					method: 'POST',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
					credentials: 'same-origin',
					body: data.toString(),
				});
				const result = await response.json();
				if (!response.ok || !result.success) {
					throw new Error('Review action failed');
				}

				button.classList.add('is-active');
				setStoredValue(storageKey, '1');
				if (mode === 'helpful') {
					const count = button.querySelector('span');
					if (count) {
						count.textContent = result.data.count;
					}
				} else {
					button.lastChild.textContent = config.reportedLabel || 'Reported';
				}
			} catch (error) {
				button.disabled = false;
			}
		});
	});

	// Mobile action delegates to the real LearnPress purchase/enrollment control.
	const mobileEnroll = root.querySelector('[data-mobile-enroll]');
	if (mobileEnroll) {
		mobileEnroll.addEventListener('click', () => {
			const action = root.querySelector('.xpc-course-actions button, .xpc-course-actions a.lp-button, .xpc-course-actions .lp-woocommerce-purchase-wrapper a');
			if (action) {
				action.click();
				return;
			}

			const card = root.querySelector('.xpc-enrollment-card');
			if (card) {
				card.scrollIntoView({ behavior: reducedMotion ? 'auto' : 'smooth', block: 'center' });
			}
		});
	}

	// Display a countdown only when LearnPress provides a genuine sale end date.
	root.querySelectorAll('[data-countdown]').forEach((countdown) => {
		const label = countdown.querySelector('[data-countdown-label]');
		const parsedDate = new Date(countdown.dataset.countdown.replace(' ', 'T'));
		if (!label || Number.isNaN(parsedDate.getTime())) {
			countdown.hidden = true;
			return;
		}

		const updateCountdown = () => {
			const seconds = Math.max(0, Math.floor((parsedDate.getTime() - Date.now()) / 1000));
			if (seconds <= 0) {
				countdown.hidden = true;
				return;
			}

			const days = Math.floor(seconds / 86400);
			const hours = Math.floor((seconds % 86400) / 3600);
			const minutes = Math.floor((seconds % 3600) / 60);
			label.textContent = days > 0 ? `${days}d ${hours}h remaining` : `${hours}h ${minutes}m remaining`;
		};

		updateCountdown();
		window.setInterval(updateCountdown, 60000);
	});

	// Subtle entrance effects stop entirely for reduced-motion users.
	if (!reducedMotion && 'IntersectionObserver' in window) {
		const revealObserver = new IntersectionObserver(
			(entries, observer) => {
				entries.forEach((entry) => {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						observer.unobserve(entry.target);
					}
				});
			},
			{ rootMargin: '0px 0px -8% 0px', threshold: 0.08 }
		);

		root.querySelectorAll('.xpc-card, .xpc-related-card').forEach((element) => {
			element.classList.add('xpc-reveal');
			revealObserver.observe(element);
		});
	}

	// A lightweight ripple gives primary actions tactile feedback.
	root.addEventListener('pointerdown', (event) => {
		const button = event.target.closest('.xpc-button, .xpc-course-actions button, .xpc-course-actions a');
		if (!button || reducedMotion) {
			return;
		}

		const bounds = button.getBoundingClientRect();
		const ripple = document.createElement('span');
		ripple.className = 'xpc-ripple';
		ripple.style.left = `${event.clientX - bounds.left}px`;
		ripple.style.top = `${event.clientY - bounds.top}px`;
		button.appendChild(ripple);
		window.setTimeout(() => ripple.remove(), 550);
	});
})();
