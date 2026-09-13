/**
 * Reveal panel – Interactivity API store.
 */
import { store, getContext } from '@wordpress/interactivity';

store( 'bwfd/reveal', {
	state: {
		get label() {
			const ctx = getContext();
			return ctx.open ? ctx.closeLabel : ctx.openLabel;
		},
	},
	actions: {
		toggle() {
			const ctx = getContext();
			ctx.open = ! ctx.open;
		},
	},
} );
