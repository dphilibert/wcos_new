# Deployment-Skript für WCOS

set :application, "WCOS"
set :repository,  "git@github.com:WEKA-Fachmedien/wcos.git"
set :scm, "git"
set :user, "deployer-colonia-wcos"
set :scm_passphrase, "pX9SZw3IpUV82372Q"

default_run_options[:pty] = true

ssh_options[:forward_agent] = true
set :git_enable_submodules, 1
set :deploy_via, :remote_cache
# set :use_sudo, false
set :scm_verbose, true

task :staging do
    role :web, "colonia.weka-fachmedien.de", :primary => true
    set :deploy_to, "/www2/staging/crn"
    set :app_environment, "staging"
    set :branch, "master"
end

task :production do
    role :web, "colonia.weka-fachmedien.de", :primary => true
    set :deploy_to, "/www2/production/crn"
    set :app_environment, "production"
    set :branch, "master"
end

task :testing do
    role :web, "colonia.weka-fachmedien.de", :primary => true
    set :deploy_to, "/srv/www/testing/wcos.weka-fachmedien.de"
    set :app_environment, "testing"
    set :branch, "master"
end

namespace :deploy do

    task :finalize_update, :except => { :no_release => true } do
        transaction do
            run "chgrp -R WWW #{releases_path}/#{release_name}"
            run "chmod -R g+w #{releases_path}/#{release_name}"
            run "cp /srv/www/testing/_config/wcos.#{app_environment}.htaccess.conf #{releases_path}/#{release_name}/public/.htaccess"

       end
    end

    task :restart, :except => { :no_release => true } do
    end

    after "deploy", :except => { :no_release => true } do
    end

end
