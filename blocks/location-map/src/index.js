import './style.scss';

import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import Save from './save';

registerBlockType('sudrezidential/location-map', {
  edit: Edit,
  save: Save,
});
