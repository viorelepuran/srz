import { useEffect, useRef, useState } from '@wordpress/element';
import { TextControl, Button, Spinner } from '@wordpress/components';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

export default function Edit({ attributes, setAttributes }) {
  const { locations = [] } = attributes;
  const [searchQuery, setSearchQuery] = useState('');
  const [searching, setSearching] = useState(false);
  const mapRef = useRef(null);
  const mapElRef = useRef(null);
  const markerRefs = useRef([]);
  const defaultLatLng = [44.3656596, 26.0970101];

  useEffect(() => {
    if (!mapElRef.current || mapRef.current) return;
    mapRef.current = L.map(mapElRef.current).setView(defaultLatLng, 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(mapRef.current);
    setTimeout(() => mapRef.current.invalidateSize(true), 300);
    locations.forEach((coord, index) => addMarker(coord, index));
  }, []);

  const addMarker = (coord, index) => {
    const [lat, lng] = coord.split(',').map(parseFloat);
    const marker = L.marker([lat, lng], { draggable: true }).addTo(mapRef.current);
    marker.on('dragend', () => {
      const { lat, lng } = marker.getLatLng();
      const updated = [...locations];
      updated[index] = `${lat.toFixed(6)},${lng.toFixed(6)}`;
      setAttributes({ locations: updated });
    });
    markerRefs.current[index] = marker;
  };

  const handleSearch = async () => {
    if (!searchQuery.trim()) return;
    setSearching(true);
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchQuery)}`;
    try {
      const res = await fetch(url);
      const data = await res.json();
      if (data && data.length > 0) {
        const { lat, lon } = data[0];
        const coord = `${parseFloat(lat).toFixed(6)},${parseFloat(lon).toFixed(6)}`;
        setAttributes({ locations: [...locations, coord] });
        addMarker(coord, locations.length);
        mapRef.current.setView([lat, lon], 15);
        setSearchQuery('');
      } else {
        alert('No results found.');
      }
    } catch (err) {
      alert('Search failed.');
    } finally {
      setSearching(false);
    }
  };

  const handleRemove = (index) => {
    const updated = [...locations];
    updated.splice(index, 1);
    setAttributes({ locations: updated });
    if (markerRefs.current[index]) {
      mapRef.current.removeLayer(markerRefs.current[index]);
      markerRefs.current.splice(index, 1);
    }
  };

  return (
    <div className="location-map-wrapper">
      <div className="location-map-search">
        <TextControl label="Search Address" value={searchQuery} onChange={setSearchQuery} />
        <Button isPrimary onClick={handleSearch} disabled={searching}>
          {searching ? <Spinner /> : 'Search & Add Marker'}
        </Button>
      </div>
      <div className="location-map-container">
        <div ref={mapElRef} className="leaflet-map"></div>
      </div>
      {locations.length > 0 && (
        <ul>
          {locations.map((loc, i) => (
            <li key={i}>{loc} <Button isLink isDestructive onClick={() => handleRemove(i)}>Remove</Button></li>
          ))}
        </ul>
      )}
    </div>
  );
}
