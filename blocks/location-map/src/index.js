import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls } from '@wordpress/block-editor';
import { useEffect, useRef } from '@wordpress/element';
import { PanelBody, TextControl, RangeControl } from '@wordpress/components';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

registerBlockType('sudrezidential/location-map', {
	apiVersion: 2,
	title: 'Location Map',
	icon: 'location-alt',
	category: 'widgets',
	attributes: {
		locations: { type: 'array', default: [] },
		mapHeight: { type: 'number', default: 450 },
		mapId: { type: 'string', default: '' }
	},

	edit({ attributes, setAttributes, clientId }) {
		const mapRef = useRef(null);
		const mapInstance = useRef(null);
		const markersRef = useRef([]);

		const { locations, mapHeight, mapId } = attributes;
		const finalMapId = mapId || `leaflet-map-${clientId.slice(0, 8)}`;

		useEffect(() => {
			if (mapRef.current && !mapInstance.current) {
				mapInstance.current = L.map(mapRef.current).setView([45.9432, 24.9668], 6);
				L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapInstance.current);

				locations.forEach(addMarker);

				mapInstance.current.on('click', (e) => {
					const target = e.originalEvent?.target;
					if (target.closest('.leaflet-marker-icon') || target.closest('.leaflet-popup')) return;

					addMarker({ lat: e.latlng.lat, lng: e.latlng.lng, info: '' });
					updateLocations();
				});

				const script = document.createElement('script');
				script.src = "https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js";
				document.head.appendChild(script);

				const style = document.createElement('link');
				style.rel = "stylesheet";
				style.href = "https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css";
				document.head.appendChild(style);

				script.onload = () => {
					L.Control.geocoder({ defaultMarkGeocode: false })
						.on('markgeocode', (e) => {
							const latlng = e.geocode.center;
							addMarker({ lat: latlng.lat, lng: latlng.lng, info: e.geocode.name });
							mapInstance.current.setView(latlng, 15);
							updateLocations();
						})
						.addTo(mapInstance.current);
				};
			}

			return () => {
				mapInstance.current?.remove();
				mapInstance.current = null;
			};
		}, []);

		const addMarker = ({ lat, lng, info }) => {
			const marker = L.marker([lat, lng], { draggable: true }).addTo(mapInstance.current);

			const updatePopup = () => {
				marker.bindPopup(`
					<textarea style="width:150px;height:70px;">${marker.options.info || ''}</textarea><br>
					<button id="save-info">Save</button>
					<button id="delete-marker" style="margin-left:5px;color:red;">Delete Marker</button>
				`).openPopup();

				setTimeout(() => {
					const popup = marker.getPopup().getElement();
					if (!popup) return;

					const textarea = popup.querySelector('textarea');
					const saveBtn = popup.querySelector('#save-info');
					const deleteBtn = popup.querySelector('#delete-marker');

					saveBtn.onclick = () => {
						marker.options.info = textarea.value;
						marker.closePopup();
						updateLocations();
					};

					deleteBtn.onclick = () => {
						mapInstance.current.removeLayer(marker);
						markersRef.current = markersRef.current.filter(m => m !== marker);
						updateLocations();
					};
				}, 0);
			};

			marker.on('click', updatePopup);
			marker.on('dragend', updateLocations);
			marker.options.info = info;
			markersRef.current.push(marker);
		};

		const updateLocations = () => {
			const newLocations = markersRef.current.map(m => ({
				lat: m.getLatLng().lat,
				lng: m.getLatLng().lng,
				info: m.options.info || '',
			}));
			setAttributes({ locations: newLocations });
		};

		return (
			<>
				<InspectorControls>
					<PanelBody title="Map Settings">
						<RangeControl
							label="Map Height (px)"
							value={mapHeight}
							onChange={(val) => setAttributes({ mapHeight: val })}
							min={200}
							max={800}
						/>
						<TextControl
							label="Map HTML ID"
							value={mapId}
							onChange={(val) => setAttributes({ mapId: val })}
							help="Leave blank to auto-generate a map ID."
						/>
					</PanelBody>
				</InspectorControls>

				<div
					id={finalMapId}
					ref={mapRef}
					style={{ height: `${mapHeight}px`, width: '100%' }}
				/>
			</>
		);
	},

	save() {
		return null; // PHP render
	}
});
