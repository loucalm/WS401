import personaFallback from "../assets/img/profil-pic/persona.jpg";

const profilePictureModules = import.meta.glob(
  "../assets/img/profil-pic/*.{png,jpg,jpeg,webp,svg}",
  {
    eager: true,
    import: "default",
  },
);

const profilePictureEntries = Object.entries(profilePictureModules).map(
  ([path, src]) => {
    const fileName = path.split("/").pop() ?? "persona.jpg";
    return {
      value: fileName,
      label: fileName.replace(/\.[^.]+$/, ""),
      src,
    };
  },
);

export const profilePictureOptions = profilePictureEntries;

export const profilePictureMap = profilePictureEntries.reduce(
  (accumulator, entry) => {
    accumulator[entry.value] = entry.src;
    return accumulator;
  },
  {},
);

export const resolveProfilePictureSrc = (value) => {
  if (!value || typeof value !== "string") {
    return personaFallback;
  }

  if (
    value.startsWith("http://") ||
    value.startsWith("https://") ||
    value.startsWith("data:")
  ) {
    return value;
  }

  const fileName = value.split("/").pop() ?? value;
  return profilePictureMap[fileName] ?? personaFallback;
};
