
import { registerBlockType } from "@wordpress/blocks";
import Edit from "./edit";
import metadata from "./block.json";

registerBlockType(metadata.name, {
  attributes: {
    locations: {
      type: "string"      
    }
  },

  /**
   * Used to construct a preview for the block to be shown in the block inserter.
   */
  /**
   * @see ./edit.js
   */
  edit: Edit,

  /**
   * @see ./save.js
   */
  save: () => null,
});
