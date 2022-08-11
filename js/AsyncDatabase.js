var AsyncDatabase = {

requestUserList: async (name) => {
  return await new Promise((resolve, reject) => {
    let xhr = new XMLHttpRequest();
    xhr.open("GET", `searchUsers.php?name=${name}`, true);
    xhr.onload = () => {
      if(xhr.status >= 200 && xhr.status < 300) {
        resolve(xhr.response);
      } else {
        reject({
          status: xhr.status,
          statusText: xhr.statusText
        });
      }
    };
    xhr.onerror = () => {
      reject({
        status: xhr.status,
        statusText: xhr.statusText
      });
    };
    xhr.send();
  });
}

};