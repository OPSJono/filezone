FROM nginx:1.17.8

COPY config/default.conf /etc/nginx/conf.d/default.conf

# Create group "devuser"
# set group id "HOST_UID" (to match host)
ARG uid=1000
RUN groupadd -g $uid devuser
# Create user "devuser"
# set user id "uid" (to match host)
# assign to existing group id "uid" (to match host)
# set home directory "/home/devuser"
# set shell "/bin/bash"
RUN useradd -d /home/devuser -s /bin/bash -u $uid -g $uid devuser
# Create home directory
RUN mkdir /home/devuser
# User and group own home directory
RUN chown -R devuser:devuser /home/devuser

# Necessary steps to avoid permission errors
RUN touch /var/run/nginx.pid \
 && chown -R devuser:devuser /var/run/nginx.pid /var/cache/nginx

USER devuser

WORKDIR /var/www

CMD ["nginx", "-g", "daemon off;"]
