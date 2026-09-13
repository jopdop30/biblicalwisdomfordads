/**
 * Endorsements carousel – Interactivity API store.
 *
 * Context (set by render.php): index, count, interval, autoplay, paused,
 * hovering, running. Each slide and dot adds its own `i`.
 */
import { store, getContext } from '@wordpress/interactivity';

const timers = new WeakMap();

const prefersReducedMotion = () =>
	typeof window !== 'undefined' &&
	typeof window.matchMedia === 'function' &&
	window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

const isPaused = ( ctx ) => ! ctx.autoplay || ctx.paused || ctx.hovering;

function clear( ctx ) {
	const t = timers.get( ctx );
	if ( t ) {
		window.clearTimeout( t.timer );
		window.clearTimeout( t.restart );
		timers.delete( ctx );
	}
}

/**
 * (Re)start the autoplay timer and the progress bar for a carousel context.
 *
 * @param {Object} ctx Carousel context (the root context, not a slide's).
 */
function schedule( ctx ) {
	clear( ctx );
	ctx.running = false;
	if ( isPaused( ctx ) ) {
		return;
	}
	// A short gap lets the browser paint the reset bar before it fills again.
	const restart = window.setTimeout( () => {
		if ( ! isPaused( ctx ) ) {
			ctx.running = true;
		}
	}, 60 );
	const timer = window.setTimeout( () => {
		ctx.index = ( ctx.index + 1 ) % ctx.count;
		schedule( ctx );
	}, ctx.interval );
	timers.set( ctx, { timer, restart } );
}

const { state, actions, callbacks } = store( 'bwfd/endorsements', {
	state: {
		get isActive() {
			const ctx = getContext();
			return ctx.index === ctx.i;
		},
		get isPaused() {
			return isPaused( getContext() );
		},
		get countLabel() {
			const ctx = getContext();
			return `${ ctx.index + 1 } of ${ ctx.count }`;
		},
		get pauseLabel() {
			return getContext().paused ? 'Play endorsements' : 'Pause endorsements';
		},
		get pauseGlyph() {
			return getContext().paused ? '▶' : '❙❙';
		},
	},
	actions: {
		next() {
			const ctx = getContext();
			ctx.index = ( ctx.index + 1 ) % ctx.count;
			schedule( ctx );
		},
		prev() {
			const ctx = getContext();
			ctx.index = ( ctx.index - 1 + ctx.count ) % ctx.count;
			schedule( ctx );
		},
		goTo() {
			const ctx = getContext();
			ctx.index = ctx.i;
			schedule( ctx );
		},
		togglePause() {
			const ctx = getContext();
			ctx.paused = ! ctx.paused;
			schedule( ctx );
		},
		hoverIn() {
			const ctx = getContext();
			if ( ! ctx.hovering ) {
				ctx.hovering = true;
				schedule( ctx );
			}
		},
		hoverOut() {
			const ctx = getContext();
			if ( ctx.hovering ) {
				ctx.hovering = false;
				schedule( ctx );
			}
		},
		onKeydown( event ) {
			if ( event.key === 'ArrowRight' ) {
				event.preventDefault();
				actions.next();
			} else if ( event.key === 'ArrowLeft' ) {
				event.preventDefault();
				actions.prev();
			}
		},
	},
	callbacks: {
		init() {
			const ctx = getContext();
			if ( prefersReducedMotion() ) {
				ctx.autoplay = false;
			}
			schedule( ctx );
			return () => clear( ctx );
		},
	},
} );

export { state, actions, callbacks };
