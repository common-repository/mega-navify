import { __ } from "@wordpress/i18n";
import { useBlockProps } from "@wordpress/block-editor";
import { SelectControl } from "@wordpress/components";
import { withSelect } from "@wordpress/data";

function Edit({ attributes, setAttributes, menuLocations }) {
  const { locations } = attributes;

  const handleLocationChange = (value) => {
    setAttributes({ locations: value });
  };

  const locationOptions = Object.entries(menuLocations).map(([index, obj]) => ({
    label: obj.description,
    value: obj.name,
  }));


  return (
    <div {...useBlockProps()}>
      <SelectControl
        label={__("Select a menu location", "your-text-domain")}
        value={locations}
        options={locationOptions}
        onChange={handleLocationChange}
      />
    </div>
  );
}

export default withSelect((select) => {
  const { getMenuLocations } = select("core");
  const menuLocations = getMenuLocations();

  return {
    menuLocations: menuLocations ?? {},
  };
})(Edit);