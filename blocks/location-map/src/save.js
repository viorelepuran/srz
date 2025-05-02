export default function Save({ attributes }) {
    const { locations } = attributes;
    return (
      <div className="location-map-wrapper" data-locations={JSON.stringify(locations)}>
        <p>Map will render here.</p>
      </div>
    );
  }
  